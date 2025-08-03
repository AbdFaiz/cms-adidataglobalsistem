<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\EmailAttachment;
use App\Services\ImapPusherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search', '');
        $currentFolder = $request->input('folder', 'INBOX');
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $emails = $this->getBaseQuery($currentFolder, $search, $sortField, $sortDirection)
            ->paginate($perPage)
            ->appends($request->query());

        $folderTitle = match ($currentFolder) {
            'INBOX' => 'Inbox',
            'SENT' => 'Sent',
            'DRAFTS' => 'Drafts',
            'TRASH' => 'Trash',
            'ARCHIVE' => 'Archive',
            default => 'Inbox'
        };

        return view('email.index', compact(
            'emails',
            'currentFolder',
            'folderTitle',
            'search',
            'perPage',
            'sortField',
            'sortDirection'
        ));
    }

    protected function getBaseQuery($folder, $search, $sortField, $sortDirection)
    {
        $query = Email::with('flags');

        if ($folder === 'TRASH') {
            $query->whereHas('flags', fn($q) => $q->where('is_trashed', true));
        } elseif ($folder === 'ARCHIVE') {
            $query->whereHas('flags', fn($q) => $q->where('is_archived', true));
        } elseif ($folder === 'SENT') {
            $query->where('folder', 'SENT')
                ->whereDoesntHave('flags', fn($q) => $q->where('is_trashed', true)->orWhere('is_archived', true));
        } elseif ($folder === 'DRAFTS') {
            $query->where('folder', 'DRAFTS')
                ->whereDoesntHave('flags', fn($q) => $q->where('is_trashed', true)->orWhere('is_archived', true));
        } else {
            $query->where('folder', 'INBOX')
                ->whereDoesntHave('flags', fn($q) => $q->where('is_trashed', true)->orWhere('is_archived', true));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%$search%")
                    ->orWhere('from_email', 'like', "%$search%")
                    ->orWhere('to_email', 'like', "%$search%")
                    ->orWhere('body', 'like', "%$search%");
            });
        }

        return $query->orderBy($sortField, $sortDirection);
    }

    public function markAsRead($id)
    {
        try {
            $email = Email::findOrFail($id);
            if ($email->status !== 'read') {
                $email->update([
                    'status' => 'read',
                    'read_at' => now(),
                    'read_by' => auth()->id()
                ]);
                return back()->with('success', 'Email marked as read!');
            }
            return back();
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function markAsUnread($id)
    {
        try {
            Email::where('id', $id)->update([
                'status' => 'unread',
                'read_at' => null,
                'read_by' => null
            ]);
            return back()->with('success', 'Email marked as unread!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $query = $this->getBaseQuery(
                $request->folder,
                $request->search,
                $request->sort_field,
                $request->sort_direction
            );

            $query->where('status', 'unread')
                ->update([
                    'status' => 'read',
                    'read_at' => now(),
                    'read_by' => auth()->id()
                ]);

            return back()->with('success', 'All emails marked as read!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function archiveEmail($id)
    {
        try {
            $email = Email::findOrFail($id);
            $email->update(['folder' => 'ARCHIVED']);
            $email->flags()->updateOrCreate([], ['is_archived' => true]);
            return back()->with('success', 'Email archived successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function moveToTrash($id)
    {
        try {
            $email = Email::findOrFail($id);
            $email->update(['folder' => 'TRASH']);
            $email->flags()->updateOrCreate([], ['is_trashed' => true]);
            return back()->with('success', 'Email moved to trash successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function restoreEmail($id)
    {
        try {
            $email = Email::findOrFail($id);
            $email->update(['folder' => 'INBOX']);
            $email->flags()->updateOrCreate([], [
                'is_trashed' => false,
                'is_archived' => false
            ]);
            return back()->with('success', 'Email restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleFlag($id)
    {
        try {
            $email = Email::findOrFail($id);
            $flags = $email->flags()->firstOrNew([]);
            $flags->is_flagged = !$flags->is_flagged;
            $flags->save();
            return back()->with('success', 'Email flag updated!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function emptyTrash()
    {
        try {
            $count = Email::whereHas('flags', fn($q) => $q->where('is_trashed', true))
                ->delete();

            return back()->with('success', 'Trash emptied successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Bulk actions
    public function bulkAction(Request $request)
    {
        $request->validate([
            'selected_emails' => 'required|array',
            'action' => 'required|in:archive,read,trash,restore'
        ]);

        try {
            $emails = Email::whereIn('id', $request->selected_emails)->get();

            DB::beginTransaction();

            foreach ($emails as $email) {
                switch ($request->action) {
                    case 'archive':
                        $email->update(['folder' => 'ARCHIVED']);
                        $email->flags()->updateOrCreate([], ['is_archived' => true]);
                        break;
                    case 'read':
                        $email->update([
                            'status' => 'read',
                            'read_at' => now(),
                            'read_by' => auth()->id()
                        ]);
                        break;
                    case 'trash':
                        $email->update(['folder' => 'TRASH']);
                        $email->flags()->updateOrCreate([], ['is_trashed' => true]);
                        break;
                    case 'restore':
                        $email->update(['folder' => 'INBOX']);
                        $email->flags()->updateOrCreate([], [
                            'is_trashed' => false,
                            'is_archived' => false
                        ]);
                        break;
                }
            }

            DB::commit();
            return back()->with('success', 'Bulk action completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function compose()
    {
        return view('email.compose');
    }

    public function showReplyForm($ticketNumber)
    {
        $emails = Email::where('ticket_number', $ticketNumber)
            ->with('attachments')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($emails->isEmpty()) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        return view('email.reply', compact('ticketNumber', 'emails'));
    }

    public function reply(Request $request, $ticketNumber)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $original = Email::where('ticket_number', $ticketNumber)->latest()->firstOrFail();

            $reply = Email::create([
                'ticket_number' => $ticketNumber,
                'from_email'    => config('mail.from.address'),
                'from_name'     => config('mail.from.name'),
                'to_email'      => $original->from_email,
                'to_name'       => $original->from_name,
                'subject'       => "Re: {$original->subject}",
                'body'          => $request->message,
                'direction'     => 'outgoing',
                'folder'        => 'SENT',
                'in_reply_to'   => $original->message_id,
            ]);

            foreach ($request->file('attachments', []) as $file) {
                EmailAttachment::create([
                    'email_id' => $reply->id,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => $file->store('email-attachments'),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            Mail::html($request->message, function ($m) use ($original, $reply) {
                $m->from(config('mail.from.address'), config('mail.from.name'))
                    ->to($original->from_email, $original->from_name)
                    ->subject("Re: {$original->subject}");

                if ($original->message_id) {
                    $m->getHeaders()->addTextHeader('In-Reply-To', $original->message_id);
                    $m->getHeaders()->addTextHeader('References', $original->message_id);
                }

                foreach ($reply->attachments as $a) {
                    $m->attach(Storage::path($a->filepath), ['as' => $a->filename]);
                }
            });

            $original->update(['status' => 'replied']);
            DB::commit();

            session()->flash('success', 'Balasan berhasil dikirim.');

            return redirect()->to(URL::signedRoute('email.detail', ['ticketNumber' => $ticketNumber]));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Reply failed: " . $e->getMessage());
            return back()->with('error', 'Gagal mengirim balasan: ' . $e->getMessage());
        }
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = EmailAttachment::findOrFail($attachmentId);

        if (!Storage::exists($attachment->filepath)) {
            abort(404, 'Attachment not found');
        }

        $headers = [
            'Content-Type' => $attachment->mime_type,
        ];

        return Storage::download($attachment->filepath, $attachment->filename, $headers);
    }

    public function webhookFromDovecot(Request $request)
    {
        if (
            $request->getUser() !== env('PUSH_USER') ||
            $request->getPassword() !== env('PUSH_PASS')
        ) {
            abort(401, 'Unauthorized');
        }

        $subject = $request->input('subject') ?? 'No Subject';

        $ticketNumber = preg_match('/(Ticket-[A-Za-z0-9]{6})\b/', $subject, $matches)
            ? $matches[1]
            : 'Ticket-' . strtoupper(substr(uniqid(), -6));

        $senderMail = $request->input('from_email');
        $senderDomain = $senderMail ? substr(strrchr($senderMail, "@"), 1) : null;

        $email = Email::create([
            'ticket_number' => $ticketNumber,
            'from_email'    => $senderMail,
            'from_name'     => $request->input('from_name'),
            'to_email'      => $request->input('to_email'),
            'to_name'       => $request->input('to_name'),
            'sender_domain' => $senderDomain,
            'status'        => 'unread',
            'priority'      => 'normal',
            'label'         => null,
            'folder'        => 'INBOX',
            'subject'       => $subject,
            'body'          => $request->input('body', ''),
            'direction'     => 'incoming',
            'message_id'    => $request->input('message_id'),
            'imap_uid'      => $request->input('imap_uid'),
            'in_reply_to'   => $request->input('in_reply_to'),
        ]);

        event(new \App\Events\NewEmailReceived($email));

        Log::info("Broadcasted new email event", [
            'id' => $email->id,
            'subject' => $email->subject
        ]);

        // (opsional) proses attachment
        // jika dari dovecot kamu bisa kirim multipart atau base64 attachment

        return response()->json(['status' => 'ok'], 200);
    }
}

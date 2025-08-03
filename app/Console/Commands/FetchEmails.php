<?php

namespace App\Console\Commands;

use App\Events\NewEmailReceived;
use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;
use App\Models\EmailAttachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FetchEmails extends Command
{
    protected $signature = 'email:fetch';
    protected $description = 'Fetch emails from INBOX and process them';

    public function handle()
    {
        Log::info('Fetching emails from INBOX...');

        try {
            $client = Client::account('default');
            $client->connect();
            $settings = config('imap.accounts.default');

            Log::info("Connected to IMAP server {$settings['host']} as {$settings['username']}");

            $folder = $client->getFolder('INBOX');

            // Ambil semua email (atau unseen, tergantung kebutuhan)
            $messages = $folder->messages()->all();

            foreach ($messages as $message) {
                $this->processNewMessage($message);
            }

            Log::info("Finished checking INBOX.");

        } catch (Throwable $e) {
            Log::error("Error fetching emails", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error("Fetch failed: " . $e->getMessage());
            return 1;
        } finally {
            if (isset($client) && $client->isConnected()) {
                $client->disconnect();
            }
        }

        return 0;
    }

    protected function processNewMessage($message)
    {
        try {
            $uid = $message->getUid();
            Log::info("Checking message with UID", ['uid' => $uid]);

            if (Email::where('imap_uid', $uid)->exists()) {
                Log::info("Email with UID {$uid} already exists, skipping.");
                return true;
            }

            $this->processEmail($message);
            return true;

        } catch (\Throwable $e) {
            Log::error("Error processing new message", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return true;
        }
    }

    protected function processEmail($message)
    {
        $uid = $message->getUid();
        $subject = $message->getSubject() ?? 'No Subject';

        $ticketNumber = preg_match('/(Ticket-[A-Za-z0-9]{6})\b/', $subject, $matches)
            ? $matches[1]
            : 'Ticket-' . strtoupper(substr(uniqid(), -6));

        $fromAddresses = $message->getFrom();
        $senderMail = $fromAddresses[0]->mail ?? null;
        $senderDomain = $senderMail ? substr(strrchr($senderMail, "@"), 1) : null;

        $email = Email::create([
            'ticket_number' => $ticketNumber,
            'from_email'    => $senderMail,
            'from_name'     => $fromAddresses[0]->personal ?? null,
            'to_email'      => $message->getTo()[0]->mail ?? null,
            'to_name'       => $message->getTo()[0]->personal ?? null,
            'sender_domain' => $senderDomain,
            'status'        => 'unread',
            'priority'      => 'normal',
            'label'         => null,
            'folder'        => 'INBOX',
            'subject'       => $subject,
            'body'          => $message->getHTMLBody() ?? $message->getTextBody() ?? '',
            'direction'     => 'incoming',
            'message_id'    => $message->getMessageId(),
            'imap_uid'      => $uid,
            'in_reply_to'   => $message->getInReplyTo() ?? null,
        ]);

        event(new NewEmailReceived($email));
        Log::info("Broadcasted new email event", [
            'id' => $email->id,
            'subject' => $email->subject
        ]);

        foreach ($message->getAttachments() as $attachment) {
            $filename = $attachment->getName();
            $filepath = 'attachments/' . uniqid() . '_' . $filename;

            Storage::disk('local')->put($filepath, $attachment->getContent());

            EmailAttachment::create([
                'email_id'  => $email->id,
                'filename'  => $filename,
                'filepath'  => $filepath,
                'mime_type' => $attachment->getMimeType(),
                'size'      => $attachment->getSize(),
            ]);
        }

        Log::info("New email processed - UID: {$uid}, Subject: '{$subject}'");
    }
}

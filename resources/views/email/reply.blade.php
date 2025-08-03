@extends('layouts.app')

@section('content')
    <div class="container-fluid pt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="fas fa-check-circle me-2"></i></span>
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="fas fa-exclamation-circle me-2"></i></span>
                <span class="alert-text">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="d-flex justify-content-start gap-3 align-items-center mb-4">
            <div>
                <a href="@signedUrl('email.index')" class="btn btn-sm btn-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <h5 class="mb-0">
                {{ $emails[0]->subject }} - <span class="text-muted">({{ $ticketNumber }})</span>
            </h5>
        </div>

        <div class="email-thread mb-4">
            @foreach ($emails as $email)
                <div class="card mb-3 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-gray-100 py-3">
                        <div class="d-flex align-items-center">
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none"
                                    data-bs-toggle="dropdown">
                                    <div
                                        class="avatar-sm bg-{{ $email->direction == 'incoming' ? 'primary' : 'secondary' }} d-flex align-items-center justify-content-center rounded me-3">
                                        <span class="small text-white fw-bold">
                                            {{ $email->direction == 'incoming' ? strtoupper(substr($email->from_email, 0, 1)) : 'CS' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="d-flex flex-column">
                                            <p class="fw-semibold mb-0">
                                                {{ $email->direction == 'incoming' ? $email->from_name : 'CS' }} <span
                                                    class="text-sm text-muted">{{ $email->direction == 'incoming' ? '<' . $email->from_email . '>' : '<' . 'cs@adidataglobalsistem.site' . '>' }}</span>
                                            </p>
                                            <span class="text-sm">{{ $email->to_email }}</span>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu p-3" style="min-width: 300px;">
                                    <div class="email-details">
                                        <div class="mb-1">
                                            <span class="text-muted">From:</span>
                                            <span>{{ $email->from_email }}</span>
                                        </div>
                                        <div class="mb-1">
                                            <span class="text-muted">To:</span>
                                            <span>{{ $email->to_email }}</span>
                                        </div>
                                        <div class="mb-1">
                                            <span class="text-muted">Date:</span>
                                            <span>{{ $email->created_at->format('D, M j, Y g:i A') }}</span>
                                        </div>
                                        <div class="mb-1">
                                            <span class="text-muted">Subject:</span>
                                            <span>{{ $email->subject }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <span class="text-muted mx-2">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                    <span class="fw-bold">{{ $email->to_email }}</span> --}}
                        </div>
                        <div class="text-muted">
                            {{ $email->created_at->format('d M Y, H:i') }}
                            <span class="badge bg-{{ $email->direction == 'incoming' ? 'primary' : 'secondary' }} ms-2">
                                {{ $email->direction == 'incoming' ? 'Incoming' : 'Outgoing' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body email-content">
                        <div class="email-body">
                            {!! $email->body !!}
                        </div>
                        @if ($email->attachments->count() > 0)
                            <div class="mt-3">
                                <h6 class="text-sm mb-2">
                                    <i class="fas fa-paperclip me-2"></i> Attachments ({{ $email->attachments->count() }})
                                </h6>
                                <div class="d-flex flex-wrap">
                                    @foreach ($email->attachments as $attachment)
                                        <div class="me-3 mb-2">
                                            <a href="{{ route('email.attachment.download', $attachment->id) }}"
                                                class="d-flex align-items-center text-sm">
                                                <i class="fas fa-file-{{ $attachment->icon }} me-2"></i>
                                                {{ $attachment->filename }}
                                                <span class="text-muted ms-2">({{ formatBytes($attachment->size) }})</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-gray-100">
                <h5 class="mb-0">
                    <i class="fas fa-reply me-2"></i> Reply to this ticket
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('email.reply', $ticketNumber) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">From</label>
                        <input type="text" class="form-control" value="cs@adidataglobalsistem.site" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <input type="text" class="form-control" value="{{ $emails->last()->from_email }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject"
                            value="Re: {{ $emails->first()->subject }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" id="emailEditor" name="message" rows="8" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <input type="file" class="form-control" name="attachments[]" multiple>
                        <div class="form-text">You can attach multiple files</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="keepCopy" name="keep_copy" checked>
                            <label class="form-check-label" for="keepCopy">
                                Keep a copy in Sent folder
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .email-thread .card {
            border-left: 3px solid #5e72e4;
        }

        .email-thread .card:nth-child(even) {
            border-left-color: #2dce89;
        }

        .email-content {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .email-body {
            max-width: 800px;
            overflow-wrap: break-word;
        }

        .email-body img {
            max-width: 100%;
            height: auto;
        }

        .avatar-sm {
            width: 36px;
            height: 36px;
        }

        .avatar-lg {
            width: 48px;
            height: 48px;
        }

        .bg-gray-100 {
            background-color: #f8f9fa;
        }

        .email-details span:not(.text-muted) {
            margin-left: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('emailEditor', {
            toolbar: [{
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', '-', 'Blockquote']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                },
                {
                    name: 'document',
                    items: ['Source']
                }
            ],
            height: 300,
            removePlugins: 'elementspath',
            resize_enabled: false
        });

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    </script>
@endpush

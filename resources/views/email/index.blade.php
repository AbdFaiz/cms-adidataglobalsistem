@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span class="alert-icon"><i class="fas fa-exclamation-circle"></i></span>
                <span class="alert-text">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header with Compose Button and Folder Tabs -->
        <div class="row align-items-center py-4">
            <div class="col-md-3 mb-3 mb-md-0">
                <a href="{{ route('email.compose') }}" class="btn btn-primary">
                    <i class="fas fa-pen me-2"></i> Compose
                </a>
            </div>

            <div class="col-md-6 mb-3 mb-md-0">
                <div class="btn-group w-100" role="group">
                    @foreach ([
            'INBOX' => 'inbox',
            'SENT' => 'paper-plane',
            'DRAFTS' => 'file-alt',
            'ARCHIVE' => 'archive',
            'TRASH' => 'trash',
        ] as $folder => $icon)
                        <a href="{{ route('email.index', ['folder' => $folder] + request()->except('folder', 'page')) }}"
                            class="btn btn-outline-primary {{ $currentFolder === $folder ? 'active' : '' }}">
                            <i class="fas fa-{{ $icon }} me-1"></i> {{ ucfirst(strtolower($folder)) }}
                            @if ($folder === 'INBOX' && $emails->count() > 0)
                                <span class="badge bg-danger ms-1">{{ $emails->count() }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-md-3 d-flex justify-content-end align-items-center gap-2">
                <!-- Bulk Action Buttons -->
                <div class="btn-group d-none d-md-flex" id="bulkActions">
                    <button class="btn btn-primary" data-action="archive" disabled title="Archive">
                        <i class="fas fa-archive"></i>
                    </button>
                    <button class="btn btn-primary" data-action="read" disabled title="Mark as Read">
                        <i class="fas fa-envelope-open"></i>
                    </button>
                    <button class="btn btn-primary" data-action="trash" disabled title="Move to Trash">
                        <i class="fas fa-trash"></i>
                    </button>
                    @if (in_array($currentFolder, ['TRASH', 'ARCHIVE']))
                        <button class="btn btn-primary" data-action="restore" disabled title="Restore">
                            <i class="fas fa-undo"></i>
                        </button>
                    @endif
                </div>

                <!-- Dropdown Menu -->
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('emails.mark-all-read') }}">
                                @csrf
                                <input type="hidden" name="folder" value="{{ $currentFolder }}">
                                <input type="hidden" name="search" value="{{ $search }}">
                                <input type="hidden" name="sort_field" value="{{ $sortField }}">
                                <input type="hidden" name="sort_direction" value="{{ $sortDirection }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-envelope-open me-2"></i> Mark All as Read
                                </button>
                            </form>
                        </li>
                        @if ($currentFolder === 'TRASH')
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('emails.empty-trash') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i> Empty Trash
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('email.index') }}">
            <input type="hidden" name="folder" value="{{ $currentFolder }}">

            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search emails..."
                            value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        @foreach ([10, 15, 25, 50] as $perPageOption)
                            <option value="{{ $perPageOption }}" {{ $perPage == $perPageOption ? 'selected' : '' }}>
                                {{ $perPageOption }} per page
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">Sort</span>
                        <select name="sort_field" class="form-select" onchange="this.form.submit()">
                            <option value="created_at" {{ $sortField === 'created_at' ? 'selected' : '' }}>Date</option>
                            <option value="subject" {{ $sortField === 'subject' ? 'selected' : '' }}>Subject</option>
                            <option value="from_email" {{ $sortField === 'from_email' ? 'selected' : '' }}>Sender</option>
                        </select>
                        <select name="sort_direction" class="form-select" onchange="this.form.submit()">
                            <option value="desc" {{ $sortDirection === 'desc' ? 'selected' : '' }}>Newest</option>
                            <option value="asc" {{ $sortDirection === 'asc' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>

        <!-- Email List -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <form id="bulkActionForm" method="POST" action="{{ route('emails.bulk') }}">
                    @csrf
                    <input type="hidden" name="action" id="bulkAction">

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40px">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>From</th>
                                    <th>Subject</th>
                                    <th width="150px">Date</th>
                                    <th width="50px"></th>
                                </tr>
                            </thead>
                            <tbody id="emailTableBody">
                                @forelse ($emails as $email)
                                    <tr class="{{ $email->status === 'unread' ? 'fw-bold' : '' }}"
                                        data-email-id="{{ $email->id }}">
                                        <td>
                                            <input type="checkbox" name="selected_emails[]" value="{{ $email->id }}"
                                                class="form-check-input email-checkbox">
                                        </td>
                                        <td>
                                            <a href="{{ route('email.detail', $email->ticket_number) }}"
                                                class="text-decoration-none">
                                                <span
                                                    class="avatar-sm bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($email->from_email, 0, 1)) }}
                                                </span>
                                                {{ Str::limit($email->from_email, 25) }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('email.detail', $email->ticket_number) }}"
                                                class="text-decoration-none">
                                                {{ $email->subject }}
                                                <small class="text-muted d-block">
                                                    {{ Str::limit(strip_tags((string) $email->body), 60) }}
                                                </small>
                                            </a>
                                        </td>
                                        <td class="text-muted">{{ $email->created_at->format('M d, H:i') }}</td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if ($email->status === 'unread')
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('emails.mark-read', $email->id) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-envelope-open me-2"></i> Mark as read
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('emails.mark-unread', $email->id) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-envelope me-2"></i> Mark as unread
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if ($currentFolder !== 'ARCHIVE')
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('emails.archive', $email->id) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-archive me-2"></i> Archive
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if ($currentFolder !== 'TRASH')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('emails.trash', $email->id) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i> Move to Trash
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if (in_array($currentFolder, ['TRASH', 'ARCHIVE']))
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form method="POST"
                                                                action="{{ route('emails.restore', $email->id) }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-undo me-2"></i> Restore
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyMessageRow">
                                        <td colspan="5" class="text-center py-5">
                                            <div class="icon icon-shape icon-lg bg-light text-primary rounded-circle mb-3">
                                                <i class="fas fa-envelope-open fa-2x"></i>
                                            </div>
                                            <h5 class="text-muted mb-1">No emails found</h5>
                                            <p class="text-muted">Your {{ strtolower($folderTitle) }} is empty</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                @if ($emails->count() > 0)
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div>
                            Showing {{ $emails->firstItem() }} to {{ $emails->lastItem() }} of {{ $emails->total() }}
                        </div>
                        <div>
                            {{ $emails->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;

            var pusher = new Pusher('6e13e94b7c48cde27be4', {
                cluster: 'ap1',
                forceTLS: true
            });

            var channel = pusher.subscribe('emails');
            channel.bind('new.email', function(data) {
                console.log('New email received:', data);
                addEmailToTable(data);
            });

            // Function to add email to table
            function addEmailToTable(data) {
                // Check if email already exists in the table
                if (document.querySelector(`tr[data-email-id="${data.id}"]`)) {
                    console.log('Email already exists in table');
                    return;
                }

                // Format the date
                const formattedDate = new Date(data.date).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Create the new row HTML
                const newRow = `
                    <tr class="fw-bold" data-email-id="${data.id}">
                        <td>
                            <input type="checkbox" name="selected_emails[]" 
                                value="${data.id}" class="form-check-input email-checkbox">
                        </td>
                        <td>
                            <a href="/emails/${data.ticket_number}" class="text-decoration-none">
                                <span class="avatar-sm bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2">
                                    ${data.from.charAt(0).toUpperCase()}
                                </span>
                                ${data.from.length > 25 ? data.from.substring(0, 25) + '...' : data.from}
                            </a>
                        </td>
                        <td>
                            <a href="/emails/${data.ticket_number}" class="text-decoration-none">
                                ${data.subject}
                                <small class="text-muted d-block">
                                    ${data.preview}
                                </small>
                            </a>
                        </td>
                        <td class="text-muted">${formattedDate}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form method="POST" action="/emails/mark-read/${data.id}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-envelope-open me-2"></i> Mark as read
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="/emails/archive/${data.id}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-archive me-2"></i> Archive
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="/emails/trash/${data.id}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i> Move to Trash
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;

                // Get the table body
                const tbody = document.getElementById('emailTableBody');

                // Remove empty message if exists
                const emptyRow = document.getElementById('emptyMessageRow');
                if (emptyRow) {
                    emptyRow.remove();
                }

                // Insert the new row at the top of the table
                tbody.insertAdjacentHTML('afterbegin', newRow);

                // Add fade-in animation
                const newRowElement = tbody.firstElementChild;
                newRowElement.style.opacity = 0;
                setTimeout(() => {
                    newRowElement.style.transition = 'opacity 0.5s ease-in-out';
                    newRowElement.style.opacity = 1;
                }, 10);

                // Update counters
                updateEmailCounters(1);

                // Show desktop notification
                showNewEmailNotification(data);
            }

            // Function to update email counters
            function updateEmailCounters(increment) {
                // Update pagination text
                const counterText = document.querySelector(
                    '.d-flex.justify-content-between.align-items-center.p-3 div:first-child');
                if (counterText) {
                    const text = counterText.textContent;
                    const matches = text.match(/Showing (\d+) to (\d+) of (\d+)/);
                    if (matches) {
                        const total = parseInt(matches[3]) + increment;
                        const newText = `Showing ${matches[1]} to ${parseInt(matches[2]) + increment} of ${total}`;
                        counterText.textContent = newText;
                    }
                }

                // Update unread count in INBOX tab
                const inboxTab = document.querySelector('.btn-outline-primary.active');
                if (inboxTab && '{{ $currentFolder }}' === 'INBOX') {
                    const badge = inboxTab.querySelector('.badge');
                    if (badge) {
                        const currentCount = parseInt(badge.textContent) || 0;
                        badge.textContent = currentCount + increment;
                    } else if (increment > 0) {
                        inboxTab.innerHTML += ` <span class="badge bg-danger ms-1">${increment}</span>`;
                    }
                }
            }

            // Function to show desktop notification
            function showNewEmailNotification(email) {
                // Check if browser supports notifications
                if (!("Notification" in window)) {
                    console.log("This browser does not support desktop notification");
                    return;
                }

                // Check if permission is already granted
                if (Notification.permission === "granted") {
                    createNotification(email);
                }
                // Otherwise, ask for permission
                else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(function(permission) {
                        if (permission === "granted") {
                            createNotification(email);
                        }
                    });
                }
            }

            // Create the actual notification
            function createNotification(email) {
                const notification = new Notification(`New email from ${email.from}`, {
                    body: email.subject,
                    icon: '/favicon.ico'
                });

                notification.onclick = function() {
                    window.location.href = `/emails/${email.ticket_number}`;
                };
            }

            // Bulk actions checkbox functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.email-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleBulkActionButtons();
            });

            // Toggle bulk action buttons based on selected emails
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('email-checkbox')) {
                    toggleBulkActionButtons();
                }
            });

            function toggleBulkActionButtons() {
                const checkedCount = document.querySelectorAll('.email-checkbox:checked').length;
                const buttons = document.querySelectorAll('#bulkActions button');

                buttons.forEach(button => {
                    button.disabled = checkedCount === 0;
                });
            }

            // Handle bulk actions
            document.querySelectorAll('#bulkActions button').forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    document.getElementById('bulkAction').value = action;
                    document.getElementById('bulkActionForm').submit();
                });
            });
        });
    </script>
@endsection

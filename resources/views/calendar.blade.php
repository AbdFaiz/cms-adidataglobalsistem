@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    <div class="py-4">
        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
            <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                <li class="breadcrumb-item">
                    <a href="#">
                        <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </a>
                </li>
                <li class="breadcrumb-item"><a href="#">Volt</a></li>
                <li class="breadcrumb-item active" aria-current="page">Calendar</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between w-100 flex-wrap">
            <div class="mb-3 mb-lg-0">
                <h1 class="h4">Calendar</h1>
                <p class="mb-0">Dozens of reusable components built to provide buttons, alerts, popovers, and more.</p>
            </div>
            <div>
                <a href="https://themesberg.com/docs/volt-bootstrap-5-dashboard/plugins/calendar/"
                    class="btn btn-outline-gray-600 d-inline-flex align-items-center">
                    <svg class="icon icon-xs me-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Calendar Docs
                </a>
                <button class="btn btn-primary ms-2" id="addEventBtn">
                    <svg class="icon icon-xs me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Event
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow">
        <div id="calendar" class="p-4"></div>
    </div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Add New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="eventForm">
                    @csrf
                    <input type="hidden" id="eventId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Event Title</label>
                            <input type="text" class="form-control" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Event Color</label>
                            <select class="form-select" id="color">
                                <option value="#3b7ddd" selected>Blue</option>
                                <option value="#1cbb8c">Green</option>
                                <option value="#fcb92c">Yellow</option>
                                <option value="#f16a5f">Red</option>
                                <option value="#6f42c1">Purple</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger me-auto" id="deleteBtn"
                            style="display: none;">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/events/fetch',
                editable: true,
                selectable: true,
                select: function(info) {
                    showModal({
                        start: info.startStr,
                        end: info.endStr
                    });
                },
                eventClick: function(info) {
                    showModal({
                        id: info.event.id,
                        title: info.event.title,
                        description: info.event.extendedProps.description,
                        start: info.event.startStr,
                        end: info.event.endStr,
                        color: info.event.backgroundColor
                    }, true);
                },
                eventDrop: function(info) {
                    updateEvent(info.event);
                },
                eventResize: function(info) {
                    updateEvent(info.event);
                }
            });
            calendar.render();

            // Show modal for adding/editing events
            function showModal(eventData, isEdit = false) {
                const modal = $('#eventModal');
                modal.find('#eventId').val(eventData.id || '');
                modal.find('#title').val(eventData.title || '');
                modal.find('#description').val(eventData.description || '');
                modal.find('#start').val(eventData.start.substring(0, 16));
                modal.find('#end').val(eventData.end ? eventData.end.substring(0, 16) : '');
                modal.find('#color').val(eventData.color || '#3b7ddd');

                modal.find('#eventModalLabel').text(isEdit ? 'Edit Event' : 'Add New Event');
                modal.find('#deleteBtn').toggle(isEdit);

                modal.modal('show');
            }

            // Save event
            $('#eventForm').on('submit', function(e) {
                e.preventDefault();

                const eventData = {
                    _token: $('input[name="_token"]').val(),
                    id: $('#eventId').val(),
                    title: $('#title').val(),
                    description: $('#description').val(),
                    start: $('#start').val(),
                    end: $('#end').val(),
                    color: $('#color').val()
                };

                const url = eventData.id ? `/events/${eventData.id}` : '/events';
                const method = eventData.id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: eventData,
                    success: function() {
                        $('#eventModal').modal('hide');
                        calendar.refetchEvents();
                        showToast('Event saved successfully', 'success');
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON.message || 'Error saving event', 'error');
                    }
                });
            });

            // Delete event
            $('#deleteBtn').on('click', function() {
                if (confirm('Are you sure you want to delete this event?')) {
                    const eventId = $('#eventId').val();

                    $.ajax({
                        url: `/events/${eventId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').val()
                        },
                        success: function() {
                            $('#eventModal').modal('hide');
                            calendar.refetchEvents();
                            showToast('Event deleted successfully', 'success');
                        },
                        error: function(xhr) {
                            showToast(xhr.responseJSON.message || 'Error deleting event',
                                'error');
                        }
                    });
                }
            });

            // Update event on drag/resize
            function updateEvent(event) {
                const eventData = {
                    _token: $('input[name="_token"]').val(),
                    id: event.id,
                    start: event.startStr,
                    end: event.endStr
                };

                $.ajax({
                    url: `/events/${event.id}`,
                    type: 'PUT',
                    data: eventData,
                    success: function() {
                        showToast('Event updated successfully', 'success');
                    },
                    error: function(xhr) {
                        calendar.refetchEvents();
                        showToast(xhr.responseJSON.message || 'Error updating event', 'error');
                    }
                });
            }

            // Add event button
            $('#addEventBtn').on('click', function() {
                showModal({
                    start: new Date().toISOString().substring(0, 16),
                    end: new Date(Date.now() + 3600000).toISOString().substring(0, 16)
                });
            });

            // Toast notification
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className =
                    `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
                toast.style.zIndex = '9999';
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');

                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;

                document.body.appendChild(toast);
                new bootstrap.Toast(toast).show();

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container-fluid px-3">
        <div class="row mt-4 mb-3">
            <div class="col-6 d-flex justify-content-between ps-0">
                <div class="me-lg-3">
                    <div class="dropdown">
                        <button class="btn btn-secondary d-inline-flex align-items-center me-2 dropdown-toggle"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Task
                        </button>
                        <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1" style="margin: 0px;">
                            <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal"
                                data-bs-target="#createTaskModal">
                                <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                    </path>
                                </svg>
                                Add Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 px-0 text-end">
                <div>
                    <button class="btn btn-gray-800">
                        <svg class="icon icon-xs text-white" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                            <path fill-rule="evenodd"
                                d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="btn btn-gray-800 text-white">
                        <svg class="icon icon-xs text-white" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="btn btn-gray-800 text-white">
                        <svg class="icon icon-xs text-white" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid kanban-container py-4 px-0">
        <div class="row d-flex flex-nowrap">
            @php
                $columns = [
                    'to_do' => ['title' => 'To Do', 'tasks' => $toDoTasks],
                    'in_progress' => ['title' => 'In Progress', 'tasks' => $inProgressTasks],
                    'done' => ['title' => 'Done', 'tasks' => $doneTasks],
                    'deployed' => ['title' => 'Deployed', 'tasks' => $deployedTasks],
                ];
            @endphp

            @foreach ($columns as $status => $column)
                <div class="col-12 col-lg-6 col-xl-4 col-xxl-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fs-6 fw-bold mb-0">{{ $column['title'] }}</h5>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm fs-6 px-1 py-0 dropdown-toggle" id="dropdownMenuLink"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z">
                                    </path>
                                </svg>
                            </button>
                            <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
                                <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal"
                                    data-bs-target="#createTaskModal" data-status="{{ $status }}">
                                    <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z">
                                        </path>
                                    </svg>
                                    Add Card
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="kanbanColumn{{ $loop->iteration }}" class="list-group kanban-list">
                        @foreach ($column['tasks'] as $task)
                            @include('components.task-card', ['task' => $task])
                        @endforeach

                        <button type="button"
                            class="btn btn-outline-gray-500 d-inline-flex align-items-center justify-content-center dashed-outline new-card w-100"
                            data-bs-toggle="modal" data-bs-target="#createTaskModal" data-status="{{ $status }}">
                            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add another card
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header pb-0 border-0">
                    <h5 class="modal-title fw-normal" id="createTaskModalLabel">Add a new task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="title"
                                placeholder="Enter a title for this card...">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="description" placeholder="Enter a description for this card..." rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <select class="form-select" name="status" id="taskStatus">
                                <option value="to_do">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                                <option value="deployed">Deployed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assignees</label>
                            <input type="text" id="userSearch" placeholder="Cari nama user..."
                                class="form-control mb-3">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @foreach ($users as $user)
                                        <tr>
                                            <td><input type="checkbox" name="users[]" value="{{ $user->id }}"></td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-3">
                            <label for="image_path" class="form-label">Upload Gambar</label>
                            <input type="file" class="form-control" name="image_path" id="image_path">
                        </div>                        
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-start">
                        <button type="button" class="btn btn-outline-gray-500" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary d-inline-flex align-items-center">
                            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add card
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add JavaScript for drag and drop functionality -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize SortableJS for each column
            @foreach ($columns as $status => $column)
                new Sortable(document.getElementById('kanbanColumn{{ $loop->iteration }}'), {
                    group: 'kanban',
                    animation: 150,
                    onEnd: function(evt) {
                        updateTaskStatus(evt.item.dataset.taskId, '{{ $status }}');
                    }
                });
            @endforeach

            // Function to update task status via AJAX
            function updateTaskStatus(taskId, status) {
                fetch(`/tasks/${taskId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            location.reload(); // Reload if there was an error
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        location.reload(); // Reload if there was an error
                    });
            }

            // Set status in create modal when clicking add buttons
            document.querySelectorAll('[data-bs-target="#createTaskModal"]').forEach(button => {
                button.addEventListener('click', function() {
                    if (this.dataset.status) {
                        document.getElementById('taskStatus').value = this.dataset.status;
                    }
                });
            });

            document.getElementById('userSearch').addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                document.querySelectorAll('#userTableBody tr').forEach(function(row) {
                    row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
                });
            });

            document.getElementById('selectAll').addEventListener('click', function() {
                let checked = this.checked;
                document.querySelectorAll('#userTableBody input[type="checkbox"]').forEach(cb => cb
                    .checked = checked);
            });
        });
    </script>
@endsection
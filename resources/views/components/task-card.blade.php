<div class="card border-0 shadow p-4 mb-3 task-card" data-task-id="{{ $task->id }}">
    <div class="card-header d-flex align-items-center justify-content-between border-0 p-0 mb-3">
        <h3 class="h5 mb-0">{{ $task->title }}</h3>
        <div>
            <div class="dropdown">
                <button type="button" class="btn btn-sm fs-6 px-1 py-0 dropdown-toggle" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <svg class="icon icon-xs text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                        <path fill-rule="evenodd"
                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
                    <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal"
                        data-bs-target="#editTaskModal{{ $task->id }}">
                        <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                            </path>
                        </svg>
                        Edit Task
                    </a>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item d-flex align-items-center">
                            <svg class="dropdown-icon text-danger me-2" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <p>{{ $task->description }}</p>
        @if ($task->progress > 0 && $task->status == 'in_progress')
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="h6 mb-0">Progress</h5>
                <div class="fw-bold small"><span>{{ $task->progress }}%</span></div>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress }}%"
                    aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        @endif
        @if ($task->users->count() > 0)
            <h5 class="fs-6 fw-normal mt-4">Assignees</h5>
            <div class="avatar-group">
                @foreach ($task->users as $user)
                    <a href="#" class="avatar" data-bs-toggle="tooltip" data-original-title="{{ $user->name }}"
                        title="">
                        <img class="rounded" alt="Image placeholder"
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random">
                    </a>
                @endforeach
            </div>
        @endif
        @if ($task->image_path)
            <div class="mt-3">
                <img src="{{ asset('storage/' . $task->image_path) }}" alt="Task Image" class="img-fluid rounded">
            </div>
        @endif
    </div>
</div>

<!-- Edit Task Modal for this task -->
<div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1"
    aria-labelledby="editTaskModalLabel{{ $task->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header pb-0 border-0">
                <h5 class="modal-title fw-normal" id="editTaskModalLabel{{ $task->id }}">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="title" value="{{ $task->title }}"
                            placeholder="Task title">
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="description" placeholder="Task description" rows="3">{{ $task->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="status">
                            <option value="to_do" {{ $task->status == 'to_do' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In
                                Progress</option>
                            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="deployed" {{ $task->status == 'deployed' ? 'selected' : '' }}>Deployed
                            </option>
                        </select>
                    </div>
                    @if ($task->status == 'in_progress')
                        <div class="mb-3">
                            <label class="form-label">Progress</label>
                            <input type="number" name="progress" id="progress" class="form-control mb-2"
                                min="0" max="100" value="{{ $task->progress }}"
                                placeholder="Progress (0-100)">
                            <div class="text-end">{{ $task->progress }}%</div>
                        </div>
                    @endif
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
                                        <td>
                                            <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                                {{ $task->users->contains($user->id) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label for="image_path_{{ $task->id }}" class="form-label">Upload Gambar</label>
                        <input type="file" class="form-control" name="image_path"
                            id="image_path_{{ $task->id }}">
                    </div>
                    @if ($task->image_path)
                        <div class="mb-3">
                            <label class="form-label">Current Image:</label><br>
                            <img src="{{ asset('storage/' . $task->image_path) }}" alt="Current Image"
                                width="100">
                        </div>
                    @endif

                </div>
                <div class="modal-footer border-0 pt-0 justify-content-start">
                    <button type="button" class="btn btn-outline-gray-500" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary d-inline-flex align-items-center">
                        <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

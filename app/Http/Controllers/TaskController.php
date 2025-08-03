<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $tasks = Task::with('users')->get()->groupBy('status');

        $users = User::where('status', 'active')->get();

        return view('tasks.index', [
            'toDoTasks' => $tasks['to_do'] ?? [],
            'inProgressTasks' => $tasks['in_progress'] ?? [],
            'doneTasks' => $tasks['done'] ?? [],
            'deployedTasks' => $tasks['deployed'] ?? [],
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,done,deployed',
            'progress' => 'nullable|integer|min:0|max:100',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ]);

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('task_images', 'public');
        }

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'progress' => $validated['progress'] ?? 0,
            'image_path' => $imagePath,
        ]);

        if ($request->has('users')) {
            $task->users()->sync($request->users);
        }

        session()->flash('success', 'Tasks created successfully.');

        return redirect()->to(URL::signedRoute('tasks.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,done,deployed',
            'progress' => 'nullable|integer|min:0|max:100',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ]);

        $imagePath = $task->image_path; // default pakai yang lama

        if ($request->hasFile('image_path')) {
            // Hapus gambar lama jika ada
            if ($task->image_path && Storage::disk('public')->exists($task->image_path)) {
                Storage::disk('public')->delete($task->image_path);
            }

            // Simpan gambar baru
            $imagePath = $request->file('image_path')->store('task_images', 'public');
        }

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'progress' => $validated['progress'] ?? 0,
            'image_path' => $imagePath,
        ]);

        if ($request->has('users')) {
            $task->users()->sync($request->users);
        }

        session()->flash('success', 'Tasks updated successfully.');

        return redirect()->to(URL::signedRoute('tasks.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        session()->flash('success', 'Tasks deleted successfully.');

        return redirect()->to(URL::signedRoute('tasks.index'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:to_do,in_progress,done,deployed',
        ]);

        $task->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }
}

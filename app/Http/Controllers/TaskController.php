<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     use AuthorizesRequests; // أضف هذا السطر

    public function index()
    {
        if (auth()->user()->isAdmin()) {
            $tasks = Task::with('images')->latest()->get();
        } else {
            $tasks = auth()->user()->tasks()->with('images')->latest()->get();
        }
    return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Task::class);
        $users = User::all();
          return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $this->authorize('create', Task::class);
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'nullable|date',
        'user_id' => 'required|exists:users,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Keep image validation for the request
    ]);

    $task = Task::create([
        'title' => $validatedData['title'],
        'description' => $validatedData['description'],
        'due_date' => $validatedData['due_date'],
        'user_id' => $validatedData['user_id'],
    ]);

    if ($request->hasFile('image')) {
        // Validate the file
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        $file = $request->file('image');

        // Additional security: verify that the file is actually an image
        $imageInfo = getimagesize($file->getRealPath());
        if (!$imageInfo) {
            return redirect()->back()->withErrors(['image' => 'Invalid image file.'])->withInput();
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->back()->withErrors(['image' => 'Invalid image file type.'])->withInput();
        }

        // Securely store the image using Laravel's storage system
        $path = $file->store('task_images', 'public');

        $task->images()->create([
            'image_path' => $path,
        ]);
    }

    return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task); // حماية المهمة
        $task->load('images'); // Eager load images for the task
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        if (auth()->user()->isAdmin()) {
            $validatedData = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|nullable|string',
                'due_date' => 'sometimes|nullable|date',
                'is_done' => 'sometimes|nullable|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Keep image validation for the request
            ]);

            $task->update([
                'title' => $validatedData['title'] ?? $task->title,
                'description' => $validatedData['description'] ?? $task->description,
                'due_date' => $validatedData['due_date'] ?? $task->due_date,
                'is_done' => $validatedData['is_done'] ?? $task->is_done,
            ]);

            if ($request->hasFile('image')) {
                // Delete old images and their files
                foreach ($task->images as $image) {
                    $oldImagePath = storage_path('app/public/' . $image->image_path);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $image->delete();
                }

                // Validate the file
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
                ]);

                $file = $request->file('image');

                // Additional security: verify that the file is actually an image
                $imageInfo = getimagesize($file->getRealPath());
                if (!$imageInfo) {
                    return redirect()->back()->withErrors(['image' => 'Invalid image file.'])->withInput();
                }

                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                if (!in_array($extension, $allowedExtensions)) {
                    return redirect()->back()->withErrors(['image' => 'Invalid image file type.'])->withInput();
                }

                // Securely store the image using Laravel's storage system
                $path = $file->store('task_images', 'public');

                $task->images()->create([
                    'image_path' => $path,
                ]);
            }
        } else {
            // Regular user can only update 'is_done' status of their own tasks
            $validatedData = $request->validate([
                'is_done' => 'sometimes|nullable|boolean',
            ]);
            $task->update(['is_done' => $validatedData['is_done'] ?? $task->is_done]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
       $this->authorize('delete', $task);

        // Delete associated images and their files
        foreach ($task->images as $image) {
            $imagePath = storage_path('app/public/' . $image->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            $image->delete();
        }

    $task->delete();
    return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
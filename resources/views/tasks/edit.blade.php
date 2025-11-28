@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Task') }}</div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task->title) }}" {{ auth()->user()->isAdmin() ? '' : 'readonly' }} required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" {{ auth()->user()->isAdmin() ? '' : 'readonly' }}>{{ old('description', $task->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $task->due_date) }}" {{ auth()->user()->isAdmin() ? '' : 'readonly' }}>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            @if($task->images->first())
                                <div>
                                    <img src="{{ asset('images/' . $task->images->first()->image_path) }}" alt="Task Image" width="200">
                                </div>
                                <a href="{{ asset('images/' . $task->images->first()->image_path) }}" download="{{ $task->images->first()->image_path }}" class="btn btn-sm btn-info mt-2">Download Image</a>
                            @else
                                <p>No image available</p>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <input type="file" name="image" id="image" class="form-control mt-2">
                            @endif
                        </div>


                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_done" id="is_done" class="form-check-input" value="1" {{ $task->is_done ? 'checked' : '' }}>
                            <label for="is_done" class="form-check-label">Completed</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Task</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

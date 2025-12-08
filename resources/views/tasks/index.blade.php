@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-0">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                    <h5 class="mb-0">{{ __('Tasks') }}</h5>
                    @can('create', App\Models\Task::class)
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-2 mb-md-0">{{ __('Add New Task') }}</a>
                    @endcan
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($tasks->count())
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Image') }}</th>
                                        @if(auth()->user()->isAdmin())
                                        <th>{{ __('Assigned To') }}</th>
                                        @endif
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                    <tr>
                                        <td class="fw-bold">{{ $task->title }}</td>
                                        <td class="text-truncate" style="max-width: 200px;">{{ $task->description }}</td>
                                        <td>
                                            @if($task->images->first())
                                                <img src="{{ Storage::url($task->images->first()->image_path) }}" alt="{{ __('Task Image') }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                            @else
                                                {{ __('No Image') }}
                                            @endif
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                        <td>{{ $task->user->name }}</td>
                                        @endif
                                        <td>{{ $task->due_date ?? __('N/A') }}</td>
                                        <td>
                                            @if($task->is_done)
                                                <span class="badge bg-success">{{ __('Done') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                                @can('delete', $task)
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to delete this task?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center mb-0">{{ __('No tasks found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

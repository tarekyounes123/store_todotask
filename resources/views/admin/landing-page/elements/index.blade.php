@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Landing Page Elements') }}</span>
                    <a href="{{ route('admin.landing-page-elements.create') }}" class="btn btn-primary">{{ __('Add New Element') }}</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Section') }}</th>
                                    <th>{{ __('Position') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($elements as $element)
                                <tr>
                                    <td>{{ $element->id }}</td>
                                    <td>{{ $element->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($element->element_type) }}</span>
                                    </td>
                                    <td>{{ $element->section->name ?? 'N/A' }}</td>
                                    <td>{{ $element->position }}</td>
                                    <td>
                                        @if($element->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.landing-page-elements.edit', $element) }}" class="btn btn-sm btn-outline-warning" title="Edit">{{ __('Edit') }}</a>
                                        <a href="{{ route('admin.landing-page-elements.show', $element) }}" class="btn btn-sm btn-outline-info" title="View">{{ __('View') }}</a>
                                        <form action="{{ route('admin.landing-page-elements.destroy', $element) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this element?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($elements->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                            <p class="mt-3">No landing page elements found. <a href="{{ route('admin.landing-page-elements.create') }}">Create your first element</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
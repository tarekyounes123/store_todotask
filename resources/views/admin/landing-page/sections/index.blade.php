@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Landing Page Sections') }}</span>
                    <a href="{{ route('admin.landing-page-sections.create') }}" class="btn btn-primary">{{ __('Add New Section') }}</a>
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
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Position') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-sections">
                                @foreach($sections as $section)
                                <tr data-id="{{ $section->id }}">
                                    <td>{{ $section->id }}</td>
                                    <td>{{ $section->name }}</td>
                                    <td>{{ $section->title }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($section->section_type) }}</span>
                                    </td>
                                    <td>{{ $section->position }}</td>
                                    <td>
                                        @if($section->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.landing-page-sections.edit', $section) }}" class="btn btn-sm btn-outline-warning" title="Edit">{{ __('Edit') }}</a>
                                        <a href="{{ route('admin.landing-page-sections.show', $section) }}" class="btn btn-sm btn-outline-info" title="View">{{ __('View') }}</a>
                                        <form action="{{ route('admin.landing-page-sections.destroy', $section) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this section? This will also delete all elements within it.')">
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

                    @if($sections->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #d1d5db;"></i>
                            <p class="mt-3">No landing page sections found. <a href="{{ route('admin.landing-page-sections.create') }}">Create your first section</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">

<script>
$(document).ready(function() {
    $("#sortable-sections").sortable({
        update: function(event, ui) {
            var sectionIds = [];
            $(this).find('tr').each(function() {
                var sectionId = $(this).data('id');
                if (sectionId) {
                    sectionIds.push(sectionId);
                }
            });

            $.ajax({
                url: '{{ route("admin.landing-page-sections.sort") }}',
                type: 'POST',
                data: {
                    sections: sectionIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        // Show success notification
                        console.log('Sections reordered successfully');
                    }
                },
                error: function(xhr) {
                    console.error('Error reordering sections:', xhr);
                }
            });
        }
    });
    $("#sortable-sections").disableSelection();
});
</script>
@endsection
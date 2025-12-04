@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Section Details: ') }} {{ $section->name }}</span>
                    <div>
                        <a href="{{ route('admin.landing-page-sections.edit', $section) }}" class="btn btn-warning">{{ __('Edit Section') }}</a>
                        <a href="{{ route('admin.landing-page-sections.index') }}" class="btn btn-secondary">{{ __('Back to Sections') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('ID') }}:</th>
                                    <td>{{ $section->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}:</th>
                                    <td>{{ $section->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Title') }}:</th>
                                    <td>{{ $section->title ?: 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type') }}:</th>
                                    <td>{{ ucfirst($section->section_type) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Position') }}:</th>
                                    <td>{{ $section->position }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        @if($section->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Content') }}</h5>
                            <div class="border p-3 bg-light">
                                {!! $section->content ?: 'No content set' !!}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>{{ __('Elements in this Section') }}</h4>
                        <a href="{{ route('admin.landing-page-elements.create') }}?section_id={{ $section->id }}" class="btn btn-primary">{{ __('Add New Element') }}</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Position') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section->elements as $element)
                                <tr>
                                    <td>{{ $element->id }}</td>
                                    <td>{{ $element->name }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($element->element_type) }}</span>
                                    </td>
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
                                        <form action="{{ route('admin.landing-page-elements.destroy', $element) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this element?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{ __('No elements in this section yet. ') }}
                                        <a href="{{ route('admin.landing-page-elements.create') }}?section_id={{ $section->id }}">{{ __('Add your first element') }}</a>.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
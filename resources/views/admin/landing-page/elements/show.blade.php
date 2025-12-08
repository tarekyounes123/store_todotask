@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Element Details: ') }} {{ $element->name }}</span>
                    <div>
                        <a href="{{ route('admin.landing-page-elements.edit', $element) }}" class="btn btn-warning">{{ __('Edit Element') }}</a>
                        <a href="{{ route('admin.landing-page-elements.index') }}" class="btn btn-secondary">{{ __('Back to Elements') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('ID') }}:</th>
                                    <td>{{ $element->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Name') }}:</th>
                                    <td>{{ $element->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type') }}:</th>
                                    <td>{{ ucfirst($element->element_type) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Section') }}:</th>
                                    <td>{{ $element->section->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Position') }}:</th>
                                    <td>{{ $element->position }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        @if($element->is_active)
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
                                {!! App\Helpers\HtmlSanitizer::sanitize($element->content, 'No content set') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
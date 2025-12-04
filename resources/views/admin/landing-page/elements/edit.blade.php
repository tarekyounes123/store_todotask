@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Landing Page Element') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.landing-page-elements.update', $element) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Element Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $element->name) }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="element_type" class="form-label">{{ __('Element Type') }}</label>
                            <select id="element_type" class="form-select @error('element_type') is-invalid @enderror" name="element_type" required>
                                <option value="text" {{ old('element_type', $element->element_type) == 'text' ? 'selected' : '' }}>Text</option>
                                <option value="heading" {{ old('element_type', $element->element_type) == 'heading' ? 'selected' : '' }}>Heading</option>
                                <option value="paragraph" {{ old('element_type', $element->element_type) == 'paragraph' ? 'selected' : '' }}>Paragraph</option>
                                <option value="image" {{ old('element_type', $element->element_type) == 'image' ? 'selected' : '' }}>Image</option>
                                <option value="button" {{ old('element_type', $element->element_type) == 'button' ? 'selected' : '' }}>Button</option>
                                <option value="icon" {{ old('element_type', $element->element_type) == 'icon' ? 'selected' : '' }}>Icon</option>
                            </select>
                            @error('element_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="section_id" class="form-label">{{ __('Section') }}</label>
                            <select id="section_id" class="form-select @error('section_id') is-invalid @enderror" name="section_id" required>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id', $element->section_id) == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Content') }}</label>
                            <textarea id="content" class="form-control @error('content') is-invalid @enderror" name="content" rows="4">{{ old('content', $element->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">{{ __('Position') }}</label>
                            <input id="position" type="number" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position', $element->position) }}" required>
                            @error('position')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', $element->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Update Element') }}
                            </button>
                            <a href="{{ route('admin.landing-page-elements.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
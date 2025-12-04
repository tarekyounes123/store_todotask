@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit Landing Page Section') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.landing-page-sections.update', $section) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Section Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $section->name) }}" required autocomplete="name" autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Section Title') }}</label>
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $section->title) }}" autocomplete="title">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="section_type" class="form-label">{{ __('Section Type') }}</label>
                            <select id="section_type" class="form-select @error('section_type') is-invalid @enderror" name="section_type" required>
                                <option value="hero" {{ old('section_type', $section->section_type) == 'hero' ? 'selected' : '' }}>Hero</option>
                                <option value="features" {{ old('section_type', $section->section_type) == 'features' ? 'selected' : '' }}>Features</option>
                                <option value="products" {{ old('section_type', $section->section_type) == 'products' ? 'selected' : '' }}>Products</option>
                                <option value="cta" {{ old('section_type', $section->section_type) == 'cta' ? 'selected' : '' }}>Call to Action</option>
                                <option value="newsletter" {{ old('section_type', $section->section_type) == 'newsletter' ? 'selected' : '' }}>Newsletter</option>
                                <option value="about" {{ old('section_type', $section->section_type) == 'about' ? 'selected' : '' }}>About Us</option>
                                <option value="contact" {{ old('section_type', $section->section_type) == 'contact' ? 'selected' : '' }}>Contact</option>
                                <option value="footer" {{ old('section_type', $section->section_type) == 'footer' ? 'selected' : '' }}>Footer</option>
                            </select>
                            @error('section_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Content') }}</label>
                            <textarea id="content" class="form-control @error('content') is-invalid @enderror" name="content" rows="5">{{ old('content', $section->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">{{ __('Position') }}</label>
                            <input id="position" type="number" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position', $section->position) }}" required>
                            @error('position')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Update Section') }}
                            </button>
                            <a href="{{ route('admin.landing-page-sections.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
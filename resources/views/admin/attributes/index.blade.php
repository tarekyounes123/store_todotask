@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Attributes</h1>
        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary mb-3">Create New Attribute</a>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Terms</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attributes as $attribute)
                    <tr>
                        <td>{{ $attribute->name }}</td>
                        <td>{{ $attribute->slug }}</td>
                        <td>
                            @foreach ($attribute->terms as $term)
                                <span class="badge bg-secondary">{{ $term->value }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
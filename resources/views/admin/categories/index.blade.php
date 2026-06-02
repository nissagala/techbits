@extends('layouts.admin')
@section('title', 'Categories — TechBits Admin')
@section('content')
<h1>Categories</h1>
<div style="max-width:600px;">
<form method="POST" action="{{ route('admin.categories.store') }}" style="display:flex;gap:.75rem;margin-bottom:1.25rem;">
    @csrf
    <input class="form-control" type="text" name="name" placeholder="New category name…" maxlength="50" required>
    <button type="submit" class="btn btn-primary btn-sm">Add</button>
</form>
<table class="data-table">
    <thead><tr><th>Name</th><th>Products</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($categories as $cat)
    <tr>
        <td>
            <form method="POST" action="{{ route('admin.categories.update', $cat) }}" style="display:flex;gap:.5rem;">
                @csrf @method('PUT')
                <input class="form-control" type="text" name="name" value="{{ $cat->name }}" maxlength="50" style="padding:.3rem .6rem;font-size:.88rem;">
                <button type="submit" class="btn btn-secondary btn-sm">Rename</button>
            </form>
        </td>
        <td>{{ $cat->products_count }}</td>
        <td>
            @if($cat->products_count === 0)
            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Delete category {{ addslashes($cat->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
            @else
            <span style="font-size:.8rem;color:var(--text-muted);">Has products — empty first</span>
            @endif
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection

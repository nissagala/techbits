@extends('layouts.admin')
@section('title', 'Products — TechBits Admin')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
    <h1>Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Add product</a>
</div>
<form method="GET" action="{{ route('admin.products.index') }}" style="display:flex;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <input class="form-control" style="width:220px;padding:.4rem .75rem;" type="text" name="search" value="{{ request('search') }}" placeholder="Search by name…">
    <select class="form-control" style="width:auto;padding:.4rem .75rem;" name="category" onchange="this.form.submit()">
        <option value="">All categories</option>
        @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
</form>
<table class="data-table">
    <thead><tr><th>Image</th><th>Name</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Flags</th><th>Actions</th></tr></thead>
    <tbody>
    @forelse($products as $p)
    <tr>
        <td>@php $img = $p->primaryImage(); @endphp
            <img src="{{ $img ? asset('storage/'.$img->path) : '' }}" style="width:48px;height:48px;object-fit:cover;border-radius:4px;background:var(--surface);">
        </td>
        <td><strong>{{ $p->name }}</strong></td>
        <td style="font-size:.82rem;color:var(--text-muted);">{{ $p->sku }}</td>
        <td>{{ $p->category->name }}</td>
        <td>@lkr($p->price)</td>
        <td>{{ $p->stock }}</td>
        <td>
            @if($p->is_featured)<span class="badge badge-featured">Featured</span>@endif
            @if($p->is_active)<span class="badge badge-active">Active</span>@else<span class="badge badge-inactive">Inactive</span>@endif
        </td>
        <td style="white-space:nowrap;">
            <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-secondary btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.products.destroy', $p) }}" style="display:inline;" onsubmit="return confirm('Delete {{ addslashes($p->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem;">No products found.</td></tr>
    @endforelse
    </tbody>
</table>
<div style="margin-top:1rem;">{{ $products->links() }}</div>
@endsection

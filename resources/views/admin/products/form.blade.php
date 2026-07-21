@extends('layouts.admin')
@section('title', isset($product) ? 'Edit Product — TechBits Admin' : 'Add Product — TechBits Admin')
@section('content')
<h1>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h1>
<form method="POST"
      action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start;">
    <div>
        <div class="form-group">
            <label>Product Name *</label>
            <input class="form-control" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" maxlength="200" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label>SKU *</label>
                <input class="form-control" type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" maxlength="50" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select class="form-control" name="category_id" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $product->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Price (LKR) *</label>
                <input class="form-control" type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="1" max="9999999" required>
            </div>
            <div class="form-group">
                <label>Stock *</label>
                <input class="form-control" type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" max="99999" required>
            </div>
        </div>
        <div class="form-group">
            <label>Short Description *</label>
            <input class="form-control" type="text" name="short_description" value="{{ old('short_description', $product->short_description ?? '') }}" maxlength="200" required>
            <div class="field-hint">10–200 characters. Shown on product cards.</div>
        </div>
        <div class="form-group">
            <label>Full Description *</label>
            <textarea class="form-control" name="description" rows="6" maxlength="5000" required>{{ old('description', $product->description ?? '') }}</textarea>
            <div class="field-hint">10–5000 characters. Plain text only.</div>
        </div>

        {{-- Specifications --}}
        <div class="form-group">
            <label>Specifications</label>
            <div id="specsContainer">
                @if(isset($product) && $product->specs->count())
                    @foreach($product->specs as $i => $spec)
                    <div class="spec-row" style="display:flex;gap:.5rem;margin-bottom:.4rem;">
                        <input class="form-control" type="text" name="specs[keys][]" value="{{ $spec->spec_key }}" placeholder="Key" maxlength="50" style="flex:1;">
                        <input class="form-control" type="text" name="specs[values][]" value="{{ $spec->spec_value }}" placeholder="Value" maxlength="200" style="flex:2;">
                        <button type="button" onclick="this.parentElement.remove()" class="btn btn-sm btn-danger">✕</button>
                    </div>
                    @endforeach
                @endif
            </div>
            <button type="button" onclick="addSpecRow()" class="btn btn-secondary btn-sm" style="margin-top:.4rem;">+ Add spec</button>
        </div>

        {{-- Images --}}
        <div class="form-group">
            <label>Images (JPG/PNG/WebP, ≤2MB each)</label>
            @if(isset($product) && $product->images->count())
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.75rem;">
                @foreach($product->images as $img)
                <div style="position:relative;">
                    <img src="{{ asset('storage/'.$img->path) }}" style="width:80px;height:80px;object-fit:cover;border-radius:4px;border:{{ $img->is_primary ? '2px solid var(--primary)' : '1px solid var(--border)' }};">
                    @if($img->is_primary)<span style="position:absolute;bottom:0;left:0;right:0;font-size:.6rem;text-align:center;background:var(--primary);color:#fff;padding:1px;">Primary</span>@endif
                    <button type="button"
                            onclick="deleteImage('img-delete-{{ $img->id }}')"
                            style="position:absolute;top:-4px;right:-4px;background:var(--error);color:#fff;border:none;border-radius:9999px;width:18px;height:18px;font-size:.7rem;cursor:pointer;line-height:18px;text-align:center;padding:0;">✕</button>
                </div>
                @endforeach
            </div>
            @endif
            <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="form-control">
        </div>
    </div>

    {{-- Right panel --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:8px;padding:1.25rem;position:sticky;top:80px;">
        <h3 style="font-size:.95rem;font-weight:700;margin-bottom:.75rem;">Product Options</h3>
        <label style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;cursor:pointer;">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            Featured (show on home page)
        </label>
        <label style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.5rem;cursor:pointer;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            Active (visible on storefront)
        </label>
        <button type="submit" class="btn btn-primary" style="width:100%;margin-bottom:.5rem;">Save product</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary" style="width:100%;display:block;text-align:center;">Cancel</a>

        @if(isset($product))
        <hr style="margin:1rem 0;border-color:var(--border);">
        <button type="button" onclick="deleteProduct()" class="btn btn-danger" style="width:100%;">Delete product</button>
        @endif
    </div>
    </div>
</form>
{{-- Delete forms live outside the main form to avoid nested-form issues --}}
@if(isset($product))
<form id="product-delete-form" method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:none;">
    @csrf @method('DELETE')
</form>
@endif
@if(isset($product) && $product->images->count())
    @foreach($product->images as $img)
    <form id="img-delete-{{ $img->id }}" method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" style="display:none;">
        @csrf @method('DELETE')
    </form>
    @endforeach
@endif

<script>
function deleteImage(formId) {
    if (!confirm('Remove image?')) return;
    document.getElementById(formId).submit();
}
function deleteProduct() {
    if (!confirm('Delete this product? This cannot be undone.')) return;
    document.getElementById('product-delete-form').submit();
}
</script>

{{-- addSpecRow() and showToast() come from public/js/app.js --}}
@endsection

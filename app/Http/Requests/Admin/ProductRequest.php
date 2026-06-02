<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        $skuRule = Rule::unique('products', 'sku')
            ->whereNull('deleted_at');
        if ($productId) {
            $skuRule = $skuRule->ignore($productId);
        }

        return [
            'name'              => 'required|string|min:3|max:200',
            'sku'               => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9\-_]+$/', $skuRule],
            'category_id'       => 'required|exists:categories,id',
            'short_description' => 'required|string|min:10|max:200',
            'description'       => 'required|string|min:10|max:5000',
            'price'             => 'required|integer|min:1|max:9999999',
            'stock'             => 'required|integer|min:0|max:99999',
            'images.*'          => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}

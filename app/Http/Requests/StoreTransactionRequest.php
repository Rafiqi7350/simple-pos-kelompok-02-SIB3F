<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'details' => ['required', 'array'],
            'details.*.product_id' => ['required', 'exists:products,id'],
            'details.*.qty' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Mengambil index dari atribut details
                    preg_match('/details\.(\d+)\.qty/', $attribute, $matches);
                    $index = $matches[1] ?? null;

                    if ($index !== null) {
                        $productId = $this->input("details.{$index}.product_id");
                        $product = Product::find($productId);

                        if ($product && $value > $product->stock) {
                            $fail("Stok produk {$product->name} tidak mencukupi (tersisa {$product->stock}).");
                        }
                    }
                },
            ],
        ];
    }
}
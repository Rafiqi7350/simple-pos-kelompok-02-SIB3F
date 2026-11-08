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
            'items' => ['required_without:details', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.qty' => [
                'required_with:items',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    preg_match('/items\.(\d+)\.qty/', $attribute, $matches);
                    $index = $matches[1] ?? null;

                    if ($index !== null) {
                        $productId = $this->input("items.{$index}.product_id");
                        $product = Product::find($productId);

                        if ($product && $value > $product->stock) {
                            $fail("Stok produk {$product->name} tidak mencukupi (tersisa {$product->stock}).");
                        }
                    }
                },
            ],

            // Aturan untuk Web POS lama (jika form lama masih memakai 'details')
            'details' => ['required_without:items', 'array', 'min:1'],
            'details.*.product_id' => ['required_with:details', 'integer', 'exists:products,id'],
            'details.*.qty' => [
                'required_with:details',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
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
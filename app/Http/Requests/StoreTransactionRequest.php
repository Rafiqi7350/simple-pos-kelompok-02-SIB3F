<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    preg_match('/items\.(\d+)\.qty/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    
                    if ($index !== null) {
                        $productId = $this->input("items.{$index}.product_id");
                        
                        if ($productId) {
                            $product = Product::find($productId);
                            
                            if ($product && $value > $product->stock) {
                                $fail("Stok untuk produk \"{$product->name}\" tidak mencukupi. Sisa stok: {$product->stock}.");
                                
                            }
                        }
                    }
                },
            ],
        ];
    }
}
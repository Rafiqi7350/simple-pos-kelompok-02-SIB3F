<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row): ?Model
    {
        if (empty($row['name']) || empty($row['category_id']) || empty($row['price'])) {
            return null;
        }

        return Product::updateOrCreate(
            [
                'name' => trim($row['name']),
                'category_id' => (int) $row['category_id'],
            ],
            [
                'price' => (int) $row['price'],
                'stock' => isset($row['stock']) ? (int) $row['stock'] : 0,
            ]
        );
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
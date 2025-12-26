<?php
namespace App\Actions;

use App\Models\Bill;
use App\Models\BillItem;

class AnalyzeBillAction
{
    public function execute(Bill $bill): void
    {
        // reset dulu (kalau re-analyze)
        $bill->items()->delete();

        $dummyItems = [
            [
                'item_name' => 'Minor Surgical Procedure',
                'category' => 'Medical Procedure',
                'price' => 1200000,
                'status' => 'danger',
                'label' => 'Potensi Phantom Billing',
                'description' => 'Prosedur tidak tercatat dalam diagnosis awal.',
            ],
            [
                'item_name' => 'Injeksi Vitamin C',
                'category' => 'Farmasi',
                'price' => 185000,
                'status' => 'review',
                'label' => 'Harga di Atas Rata-rata',
                'description' => 'Harga 45% lebih tinggi dari standar.',
            ],
            [
                'item_name' => 'Cek Darah Lengkap',
                'category' => 'Laboratorium',
                'price' => 450000,
                'status' => 'safe',
                'label' => 'Sesuai Standar',
                'description' => 'Biaya sesuai tarif referensi.',
            ],
        ];

        foreach ($dummyItems as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                ...$item,
            ]);
        }

        $bill->update([
            'status' => 'analyzed',
            'total_price' => collect($dummyItems)->sum('price'),
        ]);
    }
}

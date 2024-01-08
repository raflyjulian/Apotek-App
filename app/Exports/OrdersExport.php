<?php

namespace App\Exports;

use App\Models\order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\withMapping;

class OrdersExport implements FromCollection, WithHeadings, withMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::with('user')->get();
    }

    //nampilkan header di excel
    public function headings():array
    {
        return [
            "Nama Pembeli", "Obat", "Total Bayar", "Kasir", "Tanggal"
        ];
    }

    //map : data yang akan dimunculkkan di excelnya (sama kaya foreach di blade)
    public function map($item): array
    {
        $dataObat = '';
        foreach ($item->medicines as $value){
            //ubah object/array data dari colum medicines nya menjadi string dengn hasil: vit a (qty 2 : Rp....)
            $format = $value["name_medicine"] . "(qty " . $value['qty'] . " : Rp. " . number_format($value['sub_price']) . "),";
            $dataObat = $format;
        }
        return [
            $item->name_customer,
            $dataObat,
            $item->total_price,
            $item->user->name,
            \Carbon\carbon::parse($item->created_at)->isoFormat($item->created_at),
        ];
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\OrdersExport;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    

    public function index()
    {
        //mengambil seluruh data pada table orders dengan pagination per halaman 10 data serta mengambil hsil data relasi function bernama user pada model order
        $order = Order::with('user')->simplePaginate(10);
        return view("order.kasir.index", compact("order"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = Medicine::all();
        return view("order.kasir.create", compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_customer' => 'required',
            'medicines' => 'required',
        ]);
        // mencari jumlah item yang sama pada array, strukturnya :
        // [ "item" => "jumlah"]
        $arrayDistinct = array_count_values($request->medicines);
        //menyiapkan array kosong untuk menampung format array baru
        $arrayAssocMedicines =[];
        //looping hasil penghitungan item distinct (duplikat)
        //key akan berupa value dr input medicines (id), item array berupa jjumlah penghitungan ite, duplikat
        foreach($arrayDistinct as $id => $count) {
            //mencari data obat berdasarkan id obat yang dipilih
            $medicine = Medicine::where('id', $id)->first();
            //ambil bagian colum price dari hasil pencarian lalu kalikan dengan jumlah item duplikat sehingga akan menghasilkan total harga dari pembelian obat tersebut
            $subPrice = $medicine['price'] * $count;
            //struktur value colum medicines menjadi multidimensi dengan dimensi kedua berbentuk array assoc dengan key "id", "name_medicine", "qty", "price"
            $arrayItem =[
                "id" => $id,
                "name_medicine" => $medicine['name'],
                "qty" => $count,
                "price" => $medicine['price'],
                "sub_price" => $subPrice,
            ];
            //masukan struktur array tersebut ke array kosong yang disediakan sebumnya
            array_push($arrayAssocMedicines, $arrayItem);
        }
        //total harga pembelian dari obat bat yg dipilih
        $totalPrice = 0;
        //looping format array medicines baru
        foreach($arrayAssocMedicines as $item){
            //total harga pembelian ditambahkan dari keseluruhan sub_price data medicines
            $totalPrice += (int)$item['sub_price'];
        }
        //harga beli ditambahkan 10% ppn
        $priceWithPPN = $totalPrice + ($totalPrice*0.01);
        //tambah data ke database
        $proses = Order::create([
            //data user_id diambil dari id akun kasir yang sedang login
            'user_id' => Auth::user()->id,
            'medicines' => $arrayAssocMedicines,
            'name_customer' => $request->name_customer,
            'total_price' => $priceWithPPN,
        ]);

        if ($proses) {
            //jika proses tambah data berhasil, ambil data order yg dibuat oleh kasir yang sedang login (where), dengan tanggal paling terbaru (orderBy), ambil hanya satu data (first)
            $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();
            //kirm data order yg diambil td, bagian column if sebagai parae=meter path dari route print
            return redirect()->route('kasir.order.print', $order['id']);
        } else {
            //jika tidak berhasil, maka diarahkan kembali ke halaman form dengan pesan pemberitahuan
            return redirect()->back()->with('failed', 'Gagal membuat data pembelian. Silahkan coba kembali dengan data yang sesuai!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::find($id);
        return view('order.kasir.print', compact('order'));
    }

    public function downloadPDF($id)
    {
        //ambil yang diperlukan dan pastikan data terformat array
        $order = Order::find($id)->toArray();
        //mengirim inisia; variabel dari data yang akan digunakan pada layout pdf
        view()->share('order', $order);
        //panggilan blade yg akan di download
        $pdf = PDF::loadView('order.kasir.download-pdf', $order);
        //kembalikan atau hasilkan bentuk pdf dengan nama file tertentu
        return $pdf->download('receipt.pdf');
    }


    public function filterOrdersByDate(Request $request)
    {
        $date = $request->input('tanggal'); // Assuming 'tanggal' is the name attribute of your input field
        
        // Your logic for filtering orders by date
        $order = Order::whereDate('created_at', $date)->simplePaginate(10);; // Example logic
        
        return view('order.kasir.index', compact("order"));
    }

    public function data()
    {
        //with: mengambil hasil relasi dari pk dan fk nya. valuenya== nama func relasi hasMany/belongsTo yg ada di modelnya
        $orders = Order::with('user')->simplePaginate(5);
        return view("order.admin.index", compact('orders'));
    }

    public function exportExcel()
    {
        $file_name = 'data_pembelian.'.'xlsx';

        return Excel::download(new OrdersExport, $file_name);
    }
    
    public function filterOrdersByDateAdmin(Request $request)
    {
        $date = $request->input('tanggal'); // Assuming 'tanggal' is the name attribute of your input field
        
        // Your logic for filtering orders by date
        $orders = Order::whereDate('created_at', $date)->simplePaginate(10);; // Example logic
        
        return view('order.admin.index', compact("orders"));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}

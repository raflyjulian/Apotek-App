@extends('layouts.template')

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-between">
            <div class="col-8">
                <form action="{{ route('kasir.order.filter') }}" method="GET">
                    <div class="form-group">
                        <label for="tanggal">Cari Berdasarkan Tanggal:</label>
                        <div class="input-group" style="50%">
                            <input type="date" id="tanggal" name="tanggal" class="form-control" style="">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Cari Data</button>
                                <a href="{{ route('kasir.order.index') }}" class="btn btn-primary">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('kasir.order.create') }}" class="btn btn-primary">Pembelian baru</a>
                </div>
            </div>
        </div>
        <br>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Pembeli</th>
                    <th>Obat</th>
                    <th>Total Bayar</th>
                    <th>Kasir</th>
                    <th>Tanggal Beli</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($order as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item['name_customer'] }}</td>
                        <td>
                            <!-- karna colum medicines pada tab;e orders bertipe json yang diubah formatnya menjadi array, maka dari itu untuk mengakses/menampilkan item nyq perly menggunakan looping -->
                            @foreach ($item['medicines'] as $medicine)
                            <ol>
                                <li>
                                    <!-- mengakses key array assoc dari tiap item array value column medicines -->
                                    {{ $medicine['name_medicine']}} ( {{ number_format($medicine['price'], 0, ',','.') }} ) : Rp. {{ number_format
                                    ($medicine['sub_price'], 0, ',', '.') }} <small>qty {{ $medicine['qty'] }}</small>
                                </li>
                            </ol>
                            @endforeach
                        </td>
                        <td>Rp. {{ number_format($item['total_price'], 0, ',', '.') }}</td>
                        <!-- karna nama kasir terdapat pada table users dan relasi antara order dan users telah didefinisikan pada function relasi bernama user maka untuk mengakses column pada table users melalui relasi antara keduanya dapat dilakukan dengan $var['namaFunRelasi']['columnDariTableLainnya'] -->
                        <td>{{ $item['user']['name'] }}</td>
                        <td>{{ date('d F Y', strtotime($item['created_at'])) }}</td>
                        <td>
                            <a href="{{ route('kasir.order.download', $item['id'])}}" class="btn btn-secondary">Download Setruk</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            <!-- jika data ada atau > 0 -->
            @if ($order->count())
            <!-- muncullkan tampilan pagination -->
                {{ $order->links()}}
            @endif
        </div>
    </div>
@endsection
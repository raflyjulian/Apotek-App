@extends('layouts.template')

@section('content')
<div class="row justify-content-between">
            <div class="col-8">
                <form action="{{ route('order.filter.admin') }}" method="GET">
                    <div class="form-group">
                        <label for="tanggal">Cari Berdasarkan Tanggal:</label>
                        <div class="input-group" style="50%">
                            <input type="date" id="tanggal" name="tanggal" class="form-control" style="">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Cari Data</button>
                                <a href="{{ route('order.data') }}" class="btn btn-primary">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-4">
                <div class="d-flex justify-content-end">
                <a href="{{ route('order.export-excel') }} " class="btn btn-primary">Export Data (excel)</a>
                </div>
            </div>
        </div>
    <table class="table table-stripped table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Pembeli</th>
                <th>Obat</th>
                <th>Kasir</th>
                <th>Tanggal Pembelian</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <!-- menampilkan angka urutan berdasarkan page pagination (digunakan ketika mengambil data dengab paginate/simplePaginate) -->
                    <td>{{ ($orders->currentpage()-1) * $orders->perpage() + $loop->index + 1 }}</td>
                    <td>{{ $order->name_customer}}</td>
                    <td>
                        <!-- nested loop : didalam looping ada looping
                        karna column medicines tipe datanya erbentuk array json maka unruk mengaksesnya perly dilooping juga -->
                        <ol>
                        @foreach ($order['medicines'] as $medicine)
                            <li>
                                {{ $medicine['name_medicine'] }}
                                ( Rp. {{number_format ($medicine['price'], 0, ',', '.') }} ) :
                                Rp. {{ number_format($medicine['sub_price'], 0, ',', '.') }}
                                <small>qty {{ $medicine['qty'] }}</small>
                            </li>
                        @endforeach
                        </ol>
                    </td>
                    <td>{{ $order['user']['name'] }}</td>
                    @php
                        setlocale(LC_ALL, 'IND');
                    @endphp
                    <td>{{ Carbon\Carbon::parse($order->created_at)->formatLocalized('%d %B %Y') }}</td>
                    <td><a href="{{ route('order.download', $order['id']) }}" class="btn btn-secondary">Unduh (.pdf)</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-end">
        @if ($orders->count())
            {{ $orders->links()}}
        @endif
    </div>
@endsection
@extends('layouts.template')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@section('content')

    @if(Session::get('success'))
            <div class="alert alert-success"> {{ Session::get('success') }} </div>
    @endif
    @if(Session::get('delete'))
        <div class="alert alert-warning"> {{ Session::get('delete') }} </div>
    @endif  

    <a href="{{ route('users.create') }}" class="btn btn-secondary mb-4" style="float: right">Tambah Pengguna</a>

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($users as $item)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['email'] }}</td>
                <td>{{ $item['role'] }}</td>
                <td class="d-flex justify-content-center">
                <a href="{{ route('users.edit', $item['id'])}}" class="btn btn-primary me-3">Edit</a>

                    <button type="button" class="btn btn-danger delete-button" data-toggle="modal"data-target="#deleteModal" data-id="{{ $item['id'] }}">Hapus</button>
                </td>  
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="deleteModal" tabindex="-1" rol="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Yakin ingin menghapus akun ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function () {
            $(".delete-button").click(function () {
                var id = $(this).data('id');
                $("#deleteForm").attr('action', '{{ route("users.delete", "") }}/' + id);
            });
        });
    </script>
@endsection
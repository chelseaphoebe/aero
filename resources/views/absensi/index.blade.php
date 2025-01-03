@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h3>Absensi Pegawai</h3>
            <a href="{{ route('absensi.create') }}" class="btn btn-primary">Tambah Absensi</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensi as $index => $item)
                    <tr>
                        <td>{{ $absensi->firstItem() + $index }}</td> <!-- Nomor urut berdasarkan pagination -->
                        <td>{{ $item->pegawai->nama }}</td> <!-- Nama Pegawai -->
                        <td>{{ $item->tanggal }}</td> <!-- Tanggal Absensi -->
                        <td>{{ $item->keterangan }}</td> <!-- Keterangan -->
                        <td>
                            <!-- Aksi -->
                            <a href="{{ route('absensi.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('absensi.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $absensi->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

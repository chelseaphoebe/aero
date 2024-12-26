@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Absensi</h1>

    <!-- Menampilkan pesan berhasil jika ada -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Menampilkan pesan error jika ada -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('absensi.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="pegawai_id">Pegawai</label>
            <select class="form-control" id="pegawai_id" name="pegawai_id" required>
                @foreach($pegawais as $pegawai)
                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <!-- Tombol Kembali -->
        <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

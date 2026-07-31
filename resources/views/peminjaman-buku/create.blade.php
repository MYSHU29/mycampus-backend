@extends('layouts.app')

@section('title', 'Tambah Peminjaman Buku')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Peminjaman Buku</h1>
    <a href="{{ route('peminjaman-buku.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-plus mr-2"></i>Form Peminjaman Buku</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('peminjaman-buku.store') }}" method="POST">
            @csrf
            @include('peminjaman-buku.form')
        </form>
    </div>
</div>
@endsection

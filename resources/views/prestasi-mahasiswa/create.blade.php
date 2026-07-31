@extends('layouts.app')

@section('title', 'Tambah Prestasi Mahasiswa')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Prestasi Mahasiswa</h1>
    <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-plus mr-2"></i>Form Prestasi Mahasiswa</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('prestasi-mahasiswa.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('prestasi-mahasiswa.form')
        </form>
    </div>
</div>
@endsection

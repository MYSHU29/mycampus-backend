@extends('layouts.app')

@section('title', 'Edit Pengambilan Matakuliah')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Pengambilan Matakuliah</h1>
    <a href="{{ route('pengambilan-matakuliah.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-warning">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-edit mr-2"></i>Form Edit Pengambilan Matakuliah</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('pengambilan-matakuliah.update', $pengambilanMatakuliah->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pengambilan-matakuliah.form', ['item' => $pengambilanMatakuliah])
        </form>
    </div>
</div>
@endsection

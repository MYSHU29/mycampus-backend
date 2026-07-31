@extends('layouts.app')

@section('title', 'Edit Pembayaran SPP')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Pembayaran SPP</h1>
    <a href="{{ route('pembayaran-spp.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-warning">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-edit mr-2"></i>Form Edit Pembayaran SPP</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('pembayaran-spp.update', $pembayaranSpp->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('pembayaran-spp.form', ['item' => $pembayaranSpp])
        </form>
    </div>
</div>
@endsection

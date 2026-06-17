@extends('layouts.app')

@section('title', 'Tambah Sertifikat Karbon')
@section('page-title', 'Tambah Sertifikat Karbon')
@section('page-subtitle', 'Input data sertifikat serapan karbon')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('sertifikat-karbon.store') }}">
            @include('sertifikat-karbon._form')
        </form>
    </div>
</div>
@endsection

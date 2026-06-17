@extends('layouts.app')

@section('title', 'Edit Sertifikat Karbon')
@section('page-title', 'Edit Sertifikat Karbon')
@section('page-subtitle', 'Perbarui data sertifikat serapan karbon')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('sertifikat-karbon.update', $sertifikatKarbon) }}">
            @method('PUT')
            @include('sertifikat-karbon._form')
        </form>
    </div>
</div>
@endsection

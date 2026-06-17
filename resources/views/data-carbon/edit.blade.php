@extends('layouts.app')

@section('title', 'Edit Data Carbon')
@section('page-title', 'Edit Data Carbon')
@section('page-subtitle', 'Perbarui data emisi dan absorpsi per kecamatan')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('data-carbon.update', $carbonData) }}">
            @method('PUT')
            @include('data-carbon._form')
        </form>
    </div>
</div>
@endsection

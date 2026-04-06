@extends('layouts.app')

@section('title', 'Dashboard - Home')

@section('content')
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <h3 class="text-xl md:text-2xl font-semibold mb-4 text-gray-700">
            Bem-vindo, {{ auth()->user()->name }}!
        </h3>
        
    </div>
@endsection
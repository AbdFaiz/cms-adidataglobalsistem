@extends('layouts.errors')

@section('title', 'Server Error')

@section('content')
    <section class="vh-100 d-flex align-items-center justify-content-center">
        <img class="img-fluid w-50" src="{{ asset('assets/img/illustrations/500.svg') }}" alt="500 Server Error">
    </section>
@endsection

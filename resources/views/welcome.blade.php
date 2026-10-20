@extends('layouts.main')

@section('title', config('app.name', 'Bimbel QUALIFY') . ' — Lembaga Bimbingan Belajar')

@section('content')
    @include('partials.hero')
    @include('partials.about')
    @include('partials.features')
    @include('partials.graduation')
    @include('partials.testimonials')
    @include('partials.cta')
@endsection

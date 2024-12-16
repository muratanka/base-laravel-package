@extends('multisite::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('multisite.name') !!}</p>
@endsection

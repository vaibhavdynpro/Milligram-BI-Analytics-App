@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('image')
<img src="{{ asset('dist/img/error-image.png') }}" alt="#"   style= "margin-left: -3px;
    margin-top: -15px;"> 
@section('message', __('Server Error'))


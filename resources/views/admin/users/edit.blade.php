@extends('layouts.app')

@section('content')

<div class="container">

<h2>

Chỉnh sửa tài khoản

</h2>

<form

action="{{ route('admin.users.update',$user) }}"

method="POST"

>

@csrf

@method('PUT') 

@include('admin.users._form')

<button class="btn btn-primary">

Cập nhật

</button>

</form>

</div>

@endsection
@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">Thêm tài khoản</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">

        @csrf

        @include('admin.users._form', ['user' => new \App\Models\User()])

        <button type="submit" class="btn btn-success">
            Lưu
        </button>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            Hủy
        </a>

    </form>

</div>

@endsection
@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Thêm chuyên khoa
    </h2>

    <form
        action="{{ route('admin.specialties.store') }}"
        method="POST"
    >

        @csrf

        @include('admin.specialties._form')

        <button class="btn btn-success">
            Lưu
        </button>

        <a
            href="{{ route('admin.specialties.index') }}"
            class="btn btn-secondary"
        >
            Quay lại
        </a>

    </form>

</div>

@endsection
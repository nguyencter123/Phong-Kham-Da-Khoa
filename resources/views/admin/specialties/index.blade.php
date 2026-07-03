@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2>Quản lý chuyên khoa</h2>
            <small class="text-muted">
                Danh sách các chuyên khoa trong hệ thống
            </small>
        </div>

        <div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="btn btn-secondary"
            >
                ← Dashboard
            </a>

            <a
                href="{{ route('admin.specialties.create') }}"
                class="btn btn-primary"
            >
                + Thêm chuyên khoa
            </a>

        </div>

    </div>

    {{-- Flash message --}}
    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif

    {{-- Search --}}
    <form
        method="GET"
        class="row mb-3"
    >

        <div class="col-md-4">

            <input

                type="text"

                name="keyword"

                class="form-control"

                placeholder="Tìm tên chuyên khoa..."

                value="{{ request('keyword') }}"

            >

        </div>

        <div class="col-auto">

            <button class="btn btn-primary">

                Tìm

            </button>

        </div>

    </form>

    {{-- Table --}}

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

        <tr>

            <th width="70">
                ID
            </th>

            <th>
                Tên chuyên khoa
            </th>

            <th>
                Mô tả
            </th>

            <th width="150">
                Thao tác
            </th>

        </tr>

        </thead>

        <tbody>

        @forelse($specialties as $specialty)

            <tr>

                <td>

                    {{ $specialty->id }}

                </td>

                <td>

                    <a
                        href="{{ route('admin.specialties.show',$specialty) }}"
                    >

                        {{ $specialty->name }}

                    </a>

                </td>

                <td>

                    {{ Str::limit($specialty->description,60) }}

                </td>

                <td>

                    <a
                        href="{{ route('admin.specialties.edit',$specialty) }}"
                        class="btn btn-warning btn-sm"
                    >

                        Sửa

                    </a>

                    <form

                        action="{{ route('admin.specialties.destroy',$specialty) }}"

                        method="POST"

                        class="d-inline"

                    >

                        @csrf

                        @method('DELETE')

                        <button

                            class="btn btn-danger btn-sm"

                            onclick="return confirm('Bạn có chắc muốn xóa?')"

                        >

                            Xóa

                        </button>

                    </form>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4" class="text-center">

                    Chưa có chuyên khoa nào.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    {{ $specialties->links() }}

</div>

@endsection
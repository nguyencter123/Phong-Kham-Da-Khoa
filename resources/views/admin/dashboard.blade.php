@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">
        Admin Dashboard
    </h2>

    <div class="row g-4">

        {{-- Quản lý tài khoản --}}
        <div class="col-md-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-primary">
                    <div class="card-body text-center">
                        <h1>👥</h1>
                        <h5>Quản lý tài khoản</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Chuyên khoa --}}
        <div class="col-md-3">
            <a href="{{ route('admin.specialties.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-success">
                    <div class="card-body text-center">
                        <h1>🏥</h1>
                        <h5>Chuyên khoa</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Nhân sự --}}
        <div class="col-md-3">
            <a href="{{ route('admin.staff.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-warning">
                    <div class="card-body text-center">
                        <h1>👨‍⚕️</h1>
                        <h5>Nhân sự</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Lịch làm việc --}}
        <div class="col-md-3">
            <a href="#" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-danger">
                    <div class="card-body text-center">
                        <h1>📅</h1>
                        <h5>Lịch làm việc</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Kho thuốc --}}
        <div class="col-md-3">
            <a href="#" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-info">
                    <div class="card-body text-center">
                        <h1>💊</h1>
                        <h5>Kho thuốc</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Bệnh nhân --}}
        <div class="col-md-3">
            <a href="#" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-secondary">
                    <div class="card-body text-center">
                        <h1>🧑</h1>
                        <h5>Bệnh nhân</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Bệnh án --}}
        <div class="col-md-3">
            <a href="#" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-dark">
                    <div class="card-body text-center">
                        <h1>📋</h1>
                        <h5>Bệnh án</h5>
                    </div>
                </div>
            </a>
        </div>

        {{-- Báo cáo --}}
        <div class="col-md-3">
            <a href="#" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-primary">
                    <div class="card-body text-center">
                        <h1>📈</h1>
                        <h5>Báo cáo</h5>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary"><i class="fas fa-users-cog"></i> Quản lý Nhân sự</h3>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home"></i> Về Dashboard
            </a>
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Thêm nhân sự mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="mb-4">
                <div class="input-group" style="max-width: 400px;">
                    <input type="text" name="search" class="form-control" placeholder="Tìm tên, SĐT, Email, CCCD..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Tìm</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Họ và Tên</th>
                            <th>Vai trò</th>
                            <th>Liên hệ</th>
                            <th>CCCD</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffs as $staff)
                        <tr>
                            <td>#{{ $staff->id }}</td>
                            <td>
                                <strong>{{ $staff->name }}</strong>
                                @if($staff->role === 'doctor')
                                    <br><span class="badge bg-info text-dark">BS: {{ $staff->doctor->specialty->name ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($staff->role === 'doctor')
                                    <span class="badge bg-primary">Bác sĩ</span>
                                @elseif($staff->role === 'receptionist')
                                    <span class="badge bg-secondary">Lễ tân</span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-phone small text-muted"></i> {{ $staff->phone }}<br>
                                <i class="fas fa-envelope small text-muted"></i> {{ $staff->email }}
                            </td>
                            <td>{{ $staff->citizen_id }}</td>
                            <td>
                                @if($staff->is_active)
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Bị khóa</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa thông tin">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.staff.toggle-active', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn {{ $staff->is_active ? 'Khóa' : 'Mở khóa' }} tài khoản này?');">
                                        @csrf
                                        @method('PATCH')
                                        @if($staff->is_active)
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Khóa tài khoản">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Mở khóa tài khoản">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        @endif
                                    </form>

                                    <form action="{{ route('admin.staff.reset-password', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn cấp lại mật khẩu mặc định (1) cho người này?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Cấp lại mật khẩu">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có dữ liệu nhân sự nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $staffs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

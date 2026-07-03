@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="mb-4 text-primary"><i class="fas fa-user-shield"></i> Admin Dashboard</h3>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row">
                <!-- Lịch làm việc mẫu -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-info border-4">
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-info mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5 class="card-title fw-bold">Lịch làm việc</h5>
                            <p class="card-text text-muted">Thiết lập các ca làm việc mẫu cố định trong tuần cho các bác sĩ.</p>
                            <a href="{{ route('admin.schedules.index') }}" class="btn btn-info text-white mt-2">
                                <i class="fas fa-arrow-right"></i> Truy cập
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quản lý Nhân sự -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-primary border-4">
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-primary mb-3">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h5 class="card-title fw-bold">Quản lý Nhân sự</h5>
                            <p class="card-text text-muted">Thêm mới, chỉnh sửa thông tin, cấp lại mật khẩu và khóa tài khoản Bác sĩ, Lễ tân.</p>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-arrow-right"></i> Truy cập
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quản lý Kho thuốc -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-success border-4">
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-success mb-3">
                                <i class="fas fa-pills"></i>
                            </div>
                            <h5 class="card-title fw-bold">Danh mục thuốc</h5>
                            <p class="card-text text-muted">Quản lý kho thuốc, cập nhật đơn giá, số lượng tồn kho và hướng dẫn sử dụng.</p>
                            <a href="{{ route('admin.medicines.index') }}" class="btn btn-success mt-2">
                                <i class="fas fa-arrow-right"></i> Truy cập
                            </a>
                        </div>
                    </div>
                </div>


                <!-- Danh mục Chuyên khoa -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-danger border-4">
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-danger mb-3">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <h5 class="card-title fw-bold">Danh mục Chuyên khoa</h5>
                            <p class="card-text text-muted">Xem, thêm mới, sửa đổi thông tin và hình ảnh các chuyên khoa của phòng khám.</p>
                            <a href="{{ route('admin.specialties.index') }}" class="btn btn-danger text-white mt-2">
                                <i class="fas fa-arrow-right"></i> Truy cập
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Báo cáo Thống kê -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-0 border-start border-warning border-4">
                        <div class="card-body text-center py-4">
                            <div class="display-4 text-warning mb-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="card-title fw-bold">Báo cáo Thống kê</h5>
                            <p class="card-text text-muted">Xem báo cáo doanh thu, số lượng bệnh nhân và thống kê hoạt động phòng khám.</p>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-warning text-dark fw-bold mt-2">
                                <i class="fas fa-arrow-right"></i> Truy cập
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

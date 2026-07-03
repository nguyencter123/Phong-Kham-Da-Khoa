@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary"><i class="fas fa-user-plus"></i> Thêm Nhân sự Mới</h3>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger fw-bold">
                    <i class="fas fa-exclamation-triangle"></i> Vui lòng kiểm tra lại các lỗi sau:
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" id="roleSelect" required>
                                <option value="">-- Chọn vai trò --</option>
                                <option value="receptionist" {{ old('role') == 'receptionist' ? 'selected' : '' }}>Lễ tân</option>
                                <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Bác sĩ</option>
                            </select>
                            @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <!-- Thông tin định danh chung -->
                        <h5 class="border-bottom pb-2 mb-3 text-secondary">Thông tin cá nhân</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số CCCD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('citizen_id') is-invalid @enderror" name="citizen_id" value="{{ old('citizen_id') }}" required>
                                @error('citizen_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required>
                                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Thông tin thêm nếu là Bác sĩ -->
                        <div id="doctorFields" style="display: none;">
                            <h5 class="border-bottom pb-2 mb-3 text-secondary">Thông tin Chuyên môn (Dành cho Bác sĩ)</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Chức danh <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="VD: Ths.BS, TS.BS..." id="titleInput">
                                    @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Chuyên khoa <span class="text-danger">*</span></label>
                                    <select class="form-select @error('specialty_id') is-invalid @enderror" name="specialty_id" id="specialtySelect">
                                        <option value="">-- Chọn chuyên khoa --</option>
                                        @foreach($specialties as $spec)
                                            <option value="{{ $spec->id }}" {{ old('specialty_id') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Giới thiệu ngắn (Bio)</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" name="bio" rows="3">{{ old('bio') }}</textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Ảnh đại diện (Avatar)</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" accept="image/*">
                                <small class="text-muted">Định dạng: JPG, PNG, GIF. Tối đa: 2MB.</small>
                                @error('avatar') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Mật khẩu mặc định sau khi tạo sẽ là: <strong>1</strong>. Nhân sự có thể tự đổi lại sau khi đăng nhập.
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> Lưu nhân sự
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('roleSelect');
    const doctorFields = document.getElementById('doctorFields');
    const titleInput = document.getElementById('titleInput');
    const specialtySelect = document.getElementById('specialtySelect');

    function toggleDoctorFields() {
        if (roleSelect.value === 'doctor') {
            doctorFields.style.display = 'block';
            titleInput.setAttribute('required', 'required');
            specialtySelect.setAttribute('required', 'required');
        } else {
            doctorFields.style.display = 'none';
            titleInput.removeAttribute('required');
            specialtySelect.removeAttribute('required');
        }
    }

    // Chạy lúc mới load trang (phục hồi state nếu validation fail)
    toggleDoctorFields();

    // Chạy khi thay đổi
    roleSelect.addEventListener('change', toggleDoctorFields);
});
</script>
@endsection

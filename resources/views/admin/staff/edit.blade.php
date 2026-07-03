@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary"><i class="fas fa-user-edit"></i> Sửa Thông tin Nhân sự</h3>
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
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Vai trò</label>
                            <input type="text" class="form-control bg-light" value="{{ $staff->role === 'doctor' ? 'Bác sĩ' : 'Lễ tân' }}" readonly>
                            <input type="hidden" name="role" value="{{ $staff->role }}">
                            <small class="text-muted">Vai trò không thể thay đổi sau khi tạo.</small>
                        </div>

                        <!-- Thông tin định danh chung -->
                        <h5 class="border-bottom pb-2 mb-3 text-secondary">Thông tin cá nhân</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $staff->name) }}" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số CCCD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('citizen_id') is-invalid @enderror" name="citizen_id" value="{{ old('citizen_id', $staff->citizen_id) }}" required>
                                @error('citizen_id') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $staff->phone) }}" required>
                                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $staff->email) }}" required>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Thông tin thêm nếu là Bác sĩ -->
                        @if($staff->role === 'doctor')
                        <div id="doctorFields">
                            <h5 class="border-bottom pb-2 mb-3 text-secondary">Thông tin Chuyên môn (Dành cho Bác sĩ)</h5>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Chức danh <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $staff->doctor->title ?? '') }}" placeholder="VD: Ths.BS, TS.BS..." required>
                                    @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Chuyên khoa <span class="text-danger">*</span></label>
                                    <select class="form-select @error('specialty_id') is-invalid @enderror" name="specialty_id" required>
                                        <option value="">-- Chọn chuyên khoa --</option>
                                        @foreach($specialties as $spec)
                                            <option value="{{ $spec->id }}" {{ old('specialty_id', $staff->doctor->specialty_id ?? '') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Giới thiệu ngắn (Bio)</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" name="bio" rows="3">{{ old('bio', $staff->doctor->bio ?? '') }}</textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Ảnh đại diện (Avatar)</label>
                                @if(isset($staff->doctor) && $staff->doctor->avatar)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $staff->doctor->avatar) }}" alt="Avatar" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" name="avatar" accept="image/*">
                                <small class="text-muted">Bỏ trống nếu không muốn đổi ảnh hiện tại. Tối đa: 2MB.</small>
                                @error('avatar') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @endif

                        <div class="text-end mt-4 border-top pt-3">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save"></i> Cập nhật thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

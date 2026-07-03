@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="mb-4 text-primary"><i class="fas fa-calendar-plus"></i> Tạo Lịch Khám Vãng Lai</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
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

            <form action="{{ route('receptionist.appointments.store') }}" method="POST" id="createAppointmentForm">
                @csrf
                <div class="row">
                    <!-- Phần Thông Tin Bệnh Nhân -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-info text-white fw-bold">
                                1. Thông tin Bệnh nhân
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning small">
                                    <i class="fas fa-info-circle"></i> Nhập <strong>Số điện thoại</strong> và nhấn Tìm kiếm để tự động điền thông tin nếu là khách cũ.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone') }}" required>
                                        <button class="btn btn-outline-secondary" type="button" id="btnSearchPatient">Tìm kiếm</button>
                                    </div>
                                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Số CCCD <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('citizen_id') is-invalid @enderror" name="citizen_id" id="citizen_id" value="{{ old('citizen_id') }}" required>
                                    @error('citizen_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}" required>
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" required>
                                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div id="patientStatus" class="mt-2 fw-bold"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Phần Thông Tin Khám Bệnh -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white fw-bold">
                                2. Thông tin Khám bệnh
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ngày khám <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                    @error('date') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ca khám <span class="text-danger">*</span></label>
                                    <select class="form-select @error('shift') is-invalid @enderror" name="shift" id="shift" required>
                                        <option value="">-- Chọn ca khám --</option>
                                        <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Buổi Sáng</option>
                                        <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Buổi Chiều</option>
                                    </select>
                                    @error('shift') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chuyên khoa <span class="text-danger">*</span></label>
                                    <select class="form-select @error('specialty_id') is-invalid @enderror" name="specialty_id" id="specialty_id" required>
                                        <option value="">-- Chọn chuyên khoa --</option>
                                        @foreach($specialties as $spec)
                                            <option value="{{ $spec->id }}" {{ old('specialty_id') == $spec->id ? 'selected' : '' }}>{{ $spec->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Bác sĩ <span class="text-danger">*</span></label>
                                    <select class="form-select @error('doctor_id') is-invalid @enderror" name="doctor_id" id="doctor_id" required>
                                        <option value="">-- Vui lòng chọn Ngày, Ca và Khoa trước --</option>
                                    </select>
                                    @error('doctor_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Triệu chứng lâm sàng / Lý do khám <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('reason') is-invalid @enderror" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                    @error('reason') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <a href="{{ route('receptionist.appointments.index') }}" class="btn btn-secondary me-2">Quay lại</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5"><i class="fas fa-save"></i> Xác nhận Tạo Lịch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Logic Tìm Bệnh nhân
    const btnSearch = document.getElementById('btnSearchPatient');
    const phoneInput = document.getElementById('phone');
    const cccdInput = document.getElementById('citizen_id');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const statusDiv = document.getElementById('patientStatus');

    btnSearch.addEventListener('click', function() {
        let term = phoneInput.value.trim() || cccdInput.value.trim();
        if (!term) {
            alert('Vui lòng nhập SĐT hoặc CCCD để tìm kiếm!');
            return;
        }

        btnSearch.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        fetch(`{{ route('receptionist.api.search-patient') }}?term=${term}`)
            .then(res => res.json())
            .then(data => {
                btnSearch.innerHTML = 'Tìm kiếm';
                if (data.found) {
                    nameInput.value = data.name;
                    emailInput.value = data.email;
                    phoneInput.value = data.phone;
                    cccdInput.value = data.citizen_id;
                    
                    nameInput.readOnly = true;
                    emailInput.readOnly = true;
                    cccdInput.readOnly = true;
                    
                    statusDiv.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Đã tìm thấy khách cũ.</span>';
                } else {
                    nameInput.readOnly = false;
                    emailInput.readOnly = false;
                    cccdInput.readOnly = false;
                    nameInput.value = '';
                    emailInput.value = '';
                    cccdInput.value = '';
                    statusDiv.innerHTML = '<span class="text-primary"><i class="fas fa-user-plus"></i> Khách mới. Vui lòng nhập đầy đủ thông tin.</span>';
                }
            })
            .catch(err => {
                btnSearch.innerHTML = 'Tìm kiếm';
                statusDiv.innerHTML = '<span class="text-danger">Lỗi kết nối.</span>';
            });
    });

    // 2. Logic Tải Danh sách Bác sĩ
    const dateInput = document.getElementById('date');
    const shiftSelect = document.getElementById('shift');
    const specialtySelect = document.getElementById('specialty_id');
    const doctorSelect = document.getElementById('doctor_id');

    function loadDoctors() {
        const date = dateInput.value;
        const shift = shiftSelect.value;
        const specialty = specialtySelect.value;

        if (!date || !shift || !specialty) {
            doctorSelect.innerHTML = '<option value="">-- Vui lòng chọn Ngày, Ca và Khoa trước --</option>';
            return;
        }

        doctorSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch(`{{ route('receptionist.api.available-doctors') }}?date=${date}&shift=${shift}&specialty_id=${specialty}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    doctorSelect.innerHTML = '<option value="">-- Không có bác sĩ nào trực ca này --</option>';
                } else {
                    let html = '<option value="">-- Chọn bác sĩ --</option>';
                    data.forEach(doc => {
                        html += `<option value="${doc.id}">${doc.title} ${doc.user.name}</option>`;
                    });
                    doctorSelect.innerHTML = html;
                }
            })
            .catch(err => {
                doctorSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    }

    dateInput.addEventListener('change', loadDoctors);
    shiftSelect.addEventListener('change', loadDoctors);
    specialtySelect.addEventListener('change', loadDoctors);
});
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-info"><i class="fas fa-calendar-alt"></i> Lịch làm việc mẫu</h3>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home"></i> Về Dashboard
            </a>
            <button type="button" class="btn btn-info text-white fw-bold" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="fas fa-plus-circle"></i> Thêm ca làm việc
            </button>
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bộ lọc theo Bác sĩ -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.schedules.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Lọc theo Bác sĩ</label>
                    <select name="doctor_id" class="form-select">
                        <option value="">Tất cả bác sĩ</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->user->name }} - {{ $doctor->specialty->name ?? 'Không có khoa' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter"></i> Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách ca làm việc -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Bác sĩ</th>
                            <th>Ngày trong tuần</th>
                            <th>Ca</th>
                            <th class="text-center">Số BN tối đa</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = [0 => 'Chủ nhật', 1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7'];
                            $shifts = ['morning' => 'Sáng', 'afternoon' => 'Chiều'];
                        @endphp
                        @forelse($schedules as $schedule)
                        <tr>
                            <td class="fw-bold text-primary">{{ $schedule->doctor->user->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-secondary">{{ $days[$schedule->day_of_week] ?? 'N/A' }}</span></td>
                            <td>{{ $shifts[$schedule->shift] ?? 'N/A' }}</td>
                            <td class="text-center fw-bold text-success">{{ $schedule->max_patients_per_slot }}</td>
                            <td class="text-center">
                                @if($schedule->is_active)
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Đã khóa</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-edit-schedule" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editScheduleModal"
                                    data-id="{{ $schedule->id }}" 
                                    data-doctor="{{ $schedule->doctor_id }}" 
                                    data-day="{{ $schedule->day_of_week }}" 
                                    data-shift="{{ $schedule->shift }}" 
                                    data-max="{{ $schedule->max_patients_per_slot }}" 
                                    title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ca làm việc này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.schedules.toggle-active', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn {{ $schedule->is_active ? 'khóa' : 'mở khóa' }} ca làm việc này?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $schedule->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" title="{{ $schedule->is_active ? 'Khóa ca' : 'Mở khóa ca' }}">
                                        <i class="fas {{ $schedule->is_active ? 'fa-lock' : 'fa-unlock' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có ca làm việc nào được thiết lập.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus"></i> Thêm ca làm việc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.schedules.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn Bác sĩ <span class="text-danger">*</span></label>
                        <select class="form-select" name="doctor_id" required>
                            <option value="">-- Chọn Bác sĩ --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày trong tuần <span class="text-danger">*</span></label>
                            <select class="form-select" name="day_of_week" required>
                                <option value="1" {{ old('day_of_week') == '1' ? 'selected' : '' }}>Thứ 2</option>
                                <option value="2" {{ old('day_of_week') == '2' ? 'selected' : '' }}>Thứ 3</option>
                                <option value="3" {{ old('day_of_week') == '3' ? 'selected' : '' }}>Thứ 4</option>
                                <option value="4" {{ old('day_of_week') == '4' ? 'selected' : '' }}>Thứ 5</option>
                                <option value="5" {{ old('day_of_week') == '5' ? 'selected' : '' }}>Thứ 6</option>
                                <option value="6" {{ old('day_of_week') == '6' ? 'selected' : '' }}>Thứ 7</option>
                                <option value="0" {{ old('day_of_week') == '0' ? 'selected' : '' }}>Chủ nhật</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ca làm việc <span class="text-danger">*</span></label>
                            <select class="form-select" name="shift" required>
                                <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Ca Sáng</option>
                                <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Ca Chiều</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Số bệnh nhân tối đa <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_patients_per_slot" value="{{ old('max_patients_per_slot', 10) }}" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-info text-white fw-bold">Lưu thiết lập</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Sửa ca làm việc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editScheduleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn Bác sĩ <span class="text-danger">*</span></label>
                        <select class="form-select" name="doctor_id" id="editDoctor" required>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày trong tuần <span class="text-danger">*</span></label>
                            <select class="form-select" name="day_of_week" id="editDay" required>
                                <option value="1">Thứ 2</option>
                                <option value="2">Thứ 3</option>
                                <option value="3">Thứ 4</option>
                                <option value="4">Thứ 5</option>
                                <option value="5">Thứ 6</option>
                                <option value="6">Thứ 7</option>
                                <option value="0">Chủ nhật</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ca làm việc <span class="text-danger">*</span></label>
                            <select class="form-select" name="shift" id="editShift" required>
                                <option value="morning">Ca Sáng</option>
                                <option value="afternoon">Ca Chiều</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Số bệnh nhân tối đa <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="max_patients_per_slot" id="editMax" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBtns = document.querySelectorAll('.btn-edit-schedule');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                document.getElementById('editDoctor').value = this.getAttribute('data-doctor');
                document.getElementById('editDay').value = this.getAttribute('data-day');
                document.getElementById('editShift').value = this.getAttribute('data-shift');
                document.getElementById('editMax').value = this.getAttribute('data-max');
                
                document.getElementById('editScheduleForm').action = '/admin/schedules/' + id;
            });
        });
    });
</script>
@endsection

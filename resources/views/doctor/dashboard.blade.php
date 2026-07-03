@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <h3 class="mb-4 text-success"><i class="fas fa-stethoscope"></i> Danh Sách Hàng Chờ Khám (Hôm nay)</h3>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list-ol"></i> Bệnh nhân đang chờ</span>
                    <span class="badge bg-light text-success rounded-pill">{{ $appointments->count() }} bệnh nhân</span>
                </div>

                <div class="card-body p-0">
                    @if($appointments->count() == 0)
                        <!-- E1 - Không có bệnh nhân chờ -->
                        <div class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/1157/1157077.png" alt="Empty" width="100" class="mb-3 opacity-50">
                            <h5 class="text-muted">Hiện tại không có bệnh nhân nào trong hàng chờ.</h5>
                            <p class="text-muted mb-0">Bạn có thể nghỉ ngơi hoặc kiểm tra lại sau.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">STT</th>
                                        <th width="20%">Họ tên Bệnh nhân</th>
                                        <th width="10%">Tuổi / Giới tính</th>
                                        <th width="40%">Triệu chứng sơ bộ</th>
                                        <th class="text-center" width="25%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $index => $apt)
                                        <tr class="{{ $apt->is_priority ? 'table-warning' : '' }}">
                                            <td class="text-center fw-bold text-secondary">
                                                {{ $index + 1 }}
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $apt->patient->name ?? 'N/A' }}</div>
                                                <div class="small text-muted"><i class="fas fa-clock"></i> Ca: {{ $apt->shift == 'morning' ? 'Sáng' : 'Chiều' }}</div>
                                                @if($apt->is_priority)
                                                    <span class="badge bg-danger mt-1"><i class="fas fa-star"></i> Ưu tiên khám</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $patientProfile = $apt->patient->patient ?? null;
                                                    $age = $patientProfile && $patientProfile->date_of_birth ? \Carbon\Carbon::parse($patientProfile->date_of_birth)->age : 'N/A';
                                                    $gender = $patientProfile && $patientProfile->gender == 'male' ? 'Nam' : ($patientProfile && $patientProfile->gender == 'female' ? 'Nữ' : 'Khác');
                                                @endphp
                                                <div>{{ $age }} tuổi</div>
                                                <div class="text-muted small">{{ $gender }}</div>
                                            </td>
                                            <td>
                                                <div class="text-wrap" style="max-width: 400px; max-height: 80px; overflow-y: auto;">
                                                    {{ $apt->reason ?? 'Không ghi rõ triệu chứng.' }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($apt->status == 3)
                                                    <a href="{{ route('doctor.appointments.examine', $apt->id) }}" class="btn btn-warning btn-sm rounded-pill fw-bold px-3">
                                                        <i class="fas fa-play-circle"></i> Tiếp tục khám
                                                    </a>
                                                @else
                                                    <form action="{{ route('doctor.appointments.start', $apt->id) }}" method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-bold px-3">
                                                            <i class="fas fa-stethoscope"></i> Bắt đầu khám
                                                        </button>
                                                    </form>

                                                    <!-- E2: Nút Đẩy xuống cuối hàng -->
                                                    <form action="{{ route('doctor.appointments.push-back', $apt->id) }}" method="POST" class="d-inline-block ms-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="return confirm('Bạn có chắc chắn muốn đẩy bệnh nhân này xuống cuối hàng chờ không?')">
                                                            <i class="fas fa-arrow-down"></i> Lùi hàng
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

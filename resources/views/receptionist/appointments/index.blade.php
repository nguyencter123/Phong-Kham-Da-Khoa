@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary m-0"><i class="fas fa-users-cog"></i> Quản lý Ca Khám & Hàng Đợi (Hôm nay)</h3>
                <a href="{{ route('receptionist.appointments.create') }}" class="btn btn-success fw-bold shadow-sm">
                    <i class="fas fa-plus-circle"></i> Tạo lịch khám vãng lai
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Form Tìm kiếm -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('receptionist.appointments.index') }}" method="GET" class="d-flex align-items-center">
                        <label for="search" class="me-3 fw-bold">Tìm kiếm bệnh nhân:</label>
                        <input type="text" name="search" id="search" class="form-control me-3 w-50" placeholder="Nhập Số điện thoại hoặc Tên bệnh nhân..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        @if(request('search'))
                            <a href="{{ route('receptionist.appointments.index') }}" class="btn btn-outline-secondary ms-2">Bỏ lọc</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- E1: Không tìm thấy kết quả -->
            @if($appointments->count() == 0 && request('search'))
                <div class="alert alert-warning text-center py-4">
                    <h5>Không tìm thấy lịch hẹn cho ngày hôm nay khớp với thông tin bạn nhập.</h5>
                    <p>Bệnh nhân có thể nhớ nhầm ngày hoặc chưa đặt lịch thành công.</p>
                    <a href="{{ route('receptionist.appointments.create') }}" class="btn btn-warning mt-2">Tạo lịch khám vãng lai trực tiếp tại quầy</a>
                </div>
            @elseif($appointments->count() == 0)
                <div class="alert alert-info text-center py-4">
                    <h5>Hôm nay chưa có lịch hẹn nào.</h5>
                    <a href="{{ route('receptionist.appointments.create') }}" class="btn btn-primary mt-2">Tạo lịch khám vãng lai</a>
                </div>
            @else
                <div class="card border-primary shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <span>Danh sách Bệnh nhân hôm nay</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã BN</th>
                                        <th>Họ Tên</th>
                                        <th>Ca Khám</th>
                                        <th>Triệu chứng</th>
                                        <th>Bác sĩ</th>
                                        <th>Trạng thái</th>
                                        <th style="min-width: 200px;">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $apt)
                                        <tr class="{{ $apt->is_priority ? 'table-warning' : '' }}">
                                            <td>BN-{{ str_pad($apt->patient_id, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                <strong>{{ optional($apt->patient)->name }}</strong><br>
                                                <small>{{ optional($apt->patient)->phone }}</small>
                                                @if($apt->is_priority)
                                                    <span class="badge bg-danger ms-1" title="Bệnh nhân được dời lịch từ trước">Ưu tiên</span>
                                                @endif
                                            </td>
                                            <td>{{ $apt->shift == 'morning' ? 'Sáng' : 'Chiều' }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($apt->reason, 50) }}</td>
                                            <td>
                                                @if($apt->doctor)
                                                    {{ $apt->doctor->title }} {{ $apt->doctor->user->name }}
                                                    <br><small class="text-muted">{{ $apt->doctor->specialty->name }}</small>
                                                @else
                                                    <span class="text-danger fw-bold">Chưa phân công</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($apt->status == 0)
                                                    <span class="badge bg-secondary mb-1">Chờ duyệt</span>
                                                @elseif($apt->status == 1)
                                                    <span class="badge bg-primary mb-1">Đã duyệt</span>
                                                @elseif($apt->status == 2)
                                                    <span class="badge bg-info text-dark mb-1">Đang chờ khám</span>
                                                @elseif($apt->status == 3)
                                                    <span class="badge bg-warning text-dark mb-1">Đang khám</span>
                                                @elseif($apt->status == 4)
                                                    <span class="badge bg-danger mb-1">Chờ thanh toán</span>
                                                @elseif($apt->status == 5)
                                                    <span class="badge bg-success mb-1">Hoàn tất</span>
                                                @elseif($apt->status == 6)
                                                    <span class="badge bg-danger mb-1">Đã hủy</span>
                                                    @if($apt->cancel_reason)
                                                        <br><small class="text-muted" title="{{ $apt->cancel_reason }}">Lý do: {{ \Illuminate\Support\Str::limit($apt->cancel_reason, 20) }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    @if($apt->status < 2)
                                                        @if($apt->doctor_id)
                                                            <!-- Nhánh A: Check-in -->
                                                            <form action="{{ route('receptionist.appointments.checkin', $apt->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-check-circle"></i> Check-in
                                                                </button>
                                                            </form>
                                                        @else
                                                            <!-- Nhánh B: Phân công -->
                                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal{{ $apt->id }}">
                                                                Phân công
                                                            </button>
                                                        @endif
                                                    @endif

                                                    @if($apt->status < 3)
                                                        <!-- Nút mở Modal Dời Lịch chỉ hiện khi chưa khám -->
                                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#rescheduleModal{{ $apt->id }}">
                                                            Dời lịch
                                                        </button>

                                                        <!-- Nút mở Modal Hủy Lịch -->
                                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModalRec{{ $apt->id }}">
                                                            Hủy ca
                                                        </button>
                                                    @endif

                                                    @if($apt->status == 4)
                                                        <!-- Chuẩn bị cho UC09: Thanh toán -->
                                                        <a href="{{ route('receptionist.payment.show', $apt->id) }}" class="btn btn-sm btn-danger fw-bold">
                                                            <i class="fas fa-money-bill-wave"></i> Thanh toán
                                                        </a>
                                                    @endif
                                                    
                                                    @if($apt->status == 5)
                                                        <a href="{{ route('receptionist.invoice.print', $apt->id) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-print"></i> In Hóa đơn
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Calculate Available Doctors for this appointment's date and shift -->
                                        @php
                                            $dayOfWeek = \Carbon\Carbon::parse($apt->date)->dayOfWeek;
                                            $availableDoctors = $doctors->filter(function($doc) use ($dayOfWeek, $apt) {
                                                return $doc->schedules->where('day_of_week', $dayOfWeek)->where('shift', $apt->shift)->count() > 0;
                                            });
                                        @endphp

                                        <!-- Modal Phân Công Bác sĩ -->
                                        @if(!$apt->doctor_id)
                                        <div class="modal fade" id="assignModal{{ $apt->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Phân công & Duyệt khám</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('receptionist.appointments.assign', $apt->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label text-muted">Triệu chứng của bệnh nhân:</label>
                                                                <p class="fst-italic border p-2 bg-light">{{ $apt->reason }}</p>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Chọn Bác sĩ có lịch làm việc lúc này</label>
                                                                <select class="form-select" name="doctor_id" required>
                                                                    <option value="">-- Chọn bác sĩ --</option>
                                                                    @forelse($availableDoctors as $doc)
                                                                        <option value="{{ $doc->id }}">
                                                                            [{{ $doc->specialty->name }}] {{ $doc->title }} {{ $doc->user->name }}
                                                                        </option>
                                                                    @empty
                                                                        <option value="" disabled>Không có bác sĩ nào trực ca này</option>
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-primary" {{ $availableDoctors->isEmpty() ? 'disabled' : '' }}>Xác nhận Phân công</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Modal Dời Lịch -->
                                        <div class="modal fade" id="rescheduleModal{{ $apt->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Dời lịch khám cho BN: {{ optional($apt->patient)->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('receptionist.appointments.update', $apt->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="alert alert-info small">
                                                                Việc dời lịch sẽ tự động đánh dấu <strong>Ưu tiên</strong> cho bệnh nhân này vào ca khám mới.
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Ngày khám mới</label>
                                                                <input type="date" class="form-control" name="date" value="{{ $apt->date }}" min="{{ date('Y-m-d') }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Ca khám</label>
                                                                <select class="form-select" name="shift" required>
                                                                    <option value="morning" {{ $apt->shift == 'morning' ? 'selected' : '' }}>Buổi Sáng</option>
                                                                    <option value="afternoon" {{ $apt->shift == 'afternoon' ? 'selected' : '' }}>Buổi Chiều</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Điều chuyển Bác sĩ (Tùy chọn)</label>
                                                                <select class="form-select" name="doctor_id">
                                                                    <option value="">-- Giữ nguyên bác sĩ ban đầu --</option>
                                                                    @foreach($doctors as $doc)
                                                                        <option value="{{ $doc->id }}" {{ $apt->doctor_id == $doc->id ? 'selected' : '' }}>
                                                                            {{ $doc->specialty->name }} - {{ $doc->title }} {{ $doc->user->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-warning fw-bold">Xác nhận Dời lịch</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Hủy Lịch -->
                                        @if($apt->status < 3)
                                        <div class="modal fade" id="cancelModalRec{{ $apt->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Hủy ca khám của BN: {{ optional($apt->patient)->name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('receptionist.appointments.cancel', $apt->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body">
                                                            <p>Hành động này sẽ hủy hoàn toàn ca khám và nhường chỗ trống cho bệnh nhân khác.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">Vui lòng ghi chú lý do hủy:</label>
                                                                <textarea name="cancel_reason" class="form-control" rows="3" required placeholder="Ví dụ: Bệnh nhân không đến, Bệnh nhân xin hủy..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-danger fw-bold">Xác nhận Hủy ca</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mt-3">
                <a href="{{ route('receptionist.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection

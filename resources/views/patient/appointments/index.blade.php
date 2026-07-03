@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>{{ __('Lịch sử khám bệnh') }}</span>
                    <a href="{{ route('patient.appointments.create') }}" class="btn btn-sm btn-light text-primary fw-bold">Đặt lịch khám mới</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($appointments->count() > 0)
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Ngày khám</th>
                                    <th>Ca khám</th>
                                    <th>Bác sĩ</th>
                                    <th>Lý do khám</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $apt)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($apt->date)->format('d/m/Y') }}</td>
                                    <td>{{ $apt->shift == 'morning' ? 'Sáng' : 'Chiều' }}</td>
                                    <td>
                                        @if($apt->doctor)
                                            {{ $apt->doctor->title }} {{ $apt->doctor->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $apt->doctor->specialty->name }}</small>
                                        @else
                                            <span class="text-muted">Phòng khám sắp xếp</span>
                                        @endif
                                    </td>
                                    <td>{{ $apt->reason }}</td>
                                    <td>
                                        @if($apt->status == 0)
                                            <span class="badge bg-warning text-dark mb-1">Chờ duyệt</span>
                                        @elseif($apt->status == 1)
                                            <span class="badge bg-success mb-1">Đã duyệt</span>
                                        @elseif($apt->status == 2)
                                            <span class="badge bg-info mb-1">Đang chờ khám</span>
                                        @elseif($apt->status == 6)
                                            <span class="badge bg-danger mb-1">Đã hủy</span>
                                            @if($apt->cancel_reason)
                                                <br><small class="text-muted">Lý do: {{ $apt->cancel_reason }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary mb-1">Đã xử lý</span>
                                        @endif

                                        @if($apt->status < 3)
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $apt->id }}">
                                                    Hủy lịch
                                                </button>
                                            </div>
                                            
                                            <!-- Modal Hủy Lịch -->
                                            <div class="modal fade" id="cancelModal{{ $apt->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Xác nhận hủy lịch khám</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('patient.appointments.cancel', $apt->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body text-start text-dark">
                                                                <p>Bạn có chắc chắn muốn hủy lịch khám vào ngày <strong>{{ \Carbon\Carbon::parse($apt->date)->format('d/m/Y') }}</strong>?</p>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Vui lòng nhập lý do hủy:</label>
                                                                    <textarea name="cancel_reason" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-danger">Xác nhận Hủy</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-muted my-4">Bạn chưa có lịch hẹn nào. Hãy đặt lịch khám ngay!</p>
                        <div class="text-center">
                            <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">Đặt lịch khám</a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('patient.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary fw-bold"><i class="fas fa-pills"></i> Danh Mục Kho Thuốc</h3>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-home"></i> Về Dashboard
                    </a>
                    <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                        <i class="fas fa-plus"></i> Thêm loại thuốc mới
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Vui lòng kiểm tra lại:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Bộ lọc & Tìm kiếm -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-light">
                    <form method="GET" action="{{ route('admin.medicines.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tên thuốc...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter"></i> Lọc</button>
                        </div>
                        <div class="col-md-5 text-end text-muted small">
                            <i class="fas fa-info-circle text-info"></i> Các loại thuốc sắp hết (< 10) sẽ được bôi vàng và đẩy lên đầu danh sách.
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="20%">Tên Thuốc</th>
                                    <th width="10%">Đơn vị</th>
                                    <th width="15%" class="text-end">Đơn giá (VNĐ)</th>
                                    <th width="10%" class="text-center">Tồn kho</th>
                                    <th width="30%">HDSD Mặc định</th>
                                    <th width="15%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($medicines as $medicine)
                                <!-- E3: Bôi vàng nếu sắp hết hàng -->
                                <tr class="{{ $medicine->stock < 10 ? 'table-warning' : '' }}">
                                    <td class="fw-bold text-primary">{{ $medicine->name }}</td>
                                    <td>{{ $medicine->unit }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($medicine->price, 0, ',', '.') }} đ</td>
                                    <td class="text-center">
                                        <span class="badge {{ $medicine->stock < 10 ? 'bg-danger fs-6' : 'bg-success fs-6' }}">
                                            {{ $medicine->stock }}
                                        </span>
                                        @if($medicine->stock < 10)
                                            <div class="small text-danger mt-1 fw-bold"><i class="fas fa-exclamation-triangle"></i> Sắp hết</div>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $medicine->usage ?? 'Chưa có' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-edit-medicine" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editMedicineModal"
                                            data-id="{{ $medicine->id }}" 
                                            data-name="{{ $medicine->name }}" 
                                            data-unit="{{ $medicine->unit }}" 
                                            data-price="{{ (int)$medicine->price }}" 
                                            data-stock="{{ $medicine->stock }}" 
                                            data-usage="{{ $medicine->usage }}"
                                            title="Sửa / Cập nhật tồn kho">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <!-- Hướng C: Nút ngừng kinh doanh -->
                                        <form action="{{ route('admin.medicines.destroy', $medicine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn NGỪNG KINH DOANH loại thuốc này? Thuốc sẽ biến mất khỏi danh sách kê đơn của bác sĩ.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Ngừng kinh doanh">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-box-open fs-1 mb-3 opacity-50"></i><br>
                                        Chưa có loại thuốc nào trong kho.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-3">
                    {{ $medicines->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Thuốc -->
<div class="modal fade" id="addMedicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle"></i> Thêm loại thuốc mới vào kho</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.medicines.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Tên Thuốc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Paracetamol 500mg">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Đơn vị tính <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit" value="{{ old('unit', 'Viên') }}" required placeholder="Viên, Vỉ, Lọ...">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control text-end text-danger fw-bold" name="price" value="{{ old('price', 0) }}" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Số lượng nhập kho ban đầu <span class="text-danger">*</span></label>
                            <input type="number" class="form-control text-end text-success fw-bold" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hướng dẫn sử dụng mặc định (Tùy chọn)</label>
                        <textarea class="form-control" name="usage" rows="3" placeholder="Ví dụ: Uống 2 viên/ngày, chia 2 lần sau ăn.">{{ old('usage') }}</textarea>
                        <small class="text-muted">Mô tả này sẽ gợi ý tự động cho Bác sĩ khi kê đơn để tiết kiệm thời gian.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold"><i class="fas fa-save"></i> Lưu vào kho</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa Thuốc -->
<div class="modal fade" id="editMedicineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Cập nhật Thuốc / Nhập thêm hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMedicineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-lightbulb"></i> <strong>Mẹo:</strong> Bạn có thể cộng dồn số lượng vào ô <strong>Tồn kho</strong> để nhập thêm hàng.
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Tên Thuốc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Đơn vị tính <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="unit" id="editUnit" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control text-end text-danger fw-bold" name="price" id="editPrice" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tồn kho hiện tại <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-box"></i></span>
                                <input type="number" class="form-control text-end fw-bold" name="stock" id="editStock" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hướng dẫn sử dụng mặc định</label>
                        <textarea class="form-control" name="usage" id="editUsage" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info text-white fw-bold"><i class="fas fa-save"></i> Cập nhật thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-medicine');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const unit = this.getAttribute('data-unit');
            const price = this.getAttribute('data-price');
            const stock = this.getAttribute('data-stock');
            const usage = this.getAttribute('data-usage');
            
            document.getElementById('editMedicineForm').action = `/admin/medicines/${id}`;
            document.getElementById('editName').value = name;
            document.getElementById('editUnit').value = unit;
            document.getElementById('editPrice').value = price;
            document.getElementById('editStock').value = stock;
            document.getElementById('editUsage').value = usage;
        });
    });
});
</script>
@endsection

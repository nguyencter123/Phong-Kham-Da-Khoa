@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-warning"><i class="fas fa-stethoscope"></i> Danh mục Chuyên khoa</h3>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home"></i> Về Dashboard
            </a>
            <button type="button" class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#addSpecialtyModal">
                <i class="fas fa-plus-circle"></i> Thêm chuyên khoa
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Hình ảnh</th>
                            <th width="250">Tên chuyên khoa</th>
                            <th>Mô tả</th>
                            <th width="150" class="text-center">Số lượng Bác sĩ</th>
                            <th width="120" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($specialties as $specialty)
                        <tr>
                            <td>
                                @if($specialty->image)
                                    <img src="{{ asset('storage/' . $specialty->image) }}" alt="{{ $specialty->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center border rounded" style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted fs-4"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $specialty->name }}</td>
                            <td>{{ Str::limit($specialty->description, 100) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $specialty->doctors_count > 0 ? 'bg-primary' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                    {{ $specialty->doctors_count }} Bác sĩ
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-edit-specialty" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editSpecialtyModal"
                                    data-id="{{ $specialty->id }}" 
                                    data-name="{{ $specialty->name }}" 
                                    data-description="{{ $specialty->description }}" 
                                    title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.specialties.destroy', $specialty->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa chuyên khoa này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có chuyên khoa nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm -->
<div class="modal fade" id="addSpecialtyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus"></i> Thêm chuyên khoa mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.specialties.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên chuyên khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh đại diện</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">Lưu chuyên khoa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa -->
<div class="modal fade" id="editSpecialtyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit"></i> Sửa chuyên khoa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSpecialtyForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên chuyên khoa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hình ảnh đại diện mới</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Bỏ trống nếu không muốn đổi ảnh hiện tại.</small>
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
        const editBtns = document.querySelectorAll('.btn-edit-specialty');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const description = this.getAttribute('data-description');
                
                document.getElementById('editName').value = name;
                document.getElementById('editDescription').value = description || '';
                document.getElementById('editSpecialtyForm').action = '/admin/specialties/' + id;
            });
        });
    });
</script>
@endsection

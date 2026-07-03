@extends('layouts.app')

@section('content')

<style>
    .acc-page {
        --teal-900: #0b4f5c;
        --teal-700: #0f766e;
        --teal-600: #0ea5b7;
        --teal-100: #e6f7f6;
        --slate-900: #1e293b;
        --slate-600: #5b6b7c;
        --slate-400: #94a3b8;
        --slate-200: #e6ebf0;
        --slate-50: #f8fafc;
        --amber-600: #d97706;
        --amber-50: #fff7ec;
        --red-600: #dc2626;
        --red-50: #fef2f2;
        --green-600: #16a34a;
        --green-50: #f0fdf4;
        --blue-600: #2563eb;
        --blue-50: #eff6ff;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        color: var(--slate-900);
    }

    .acc-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .acc-header .acc-title-block {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .acc-icon-badge {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--teal-600), var(--teal-900));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        flex-shrink: 0;
        box-shadow: 0 6px 16px -6px rgba(15, 118, 110, 0.55);
    }

    .acc-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: -0.02em;
    }

    .acc-header .acc-subtitle {
        margin: 2px 0 0;
        font-size: 0.85rem;
        color: var(--slate-600);
    }

    .acc-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-acc {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none;
        border: 1px solid transparent;
        transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
    }

    .btn-acc:hover { transform: translateY(-1px); text-decoration: none; }

    .btn-acc-outline {
        background: #fff;
        border-color: var(--slate-200);
        color: var(--slate-600);
    }
    .btn-acc-outline:hover { border-color: var(--slate-400); color: var(--slate-900); }

    .btn-acc-solid {
        background: linear-gradient(135deg, var(--teal-600), var(--teal-700));
        color: #fff;
        box-shadow: 0 8px 18px -8px rgba(15, 118, 110, 0.6);
    }
    .btn-acc-solid:hover { color: #fff; box-shadow: 0 10px 22px -6px rgba(15, 118, 110, 0.7); }

    .acc-filter-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 22px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .acc-filter-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--slate-400);
        margin-bottom: 6px;
        display: block;
    }

    .acc-filter-card .form-control,
    .acc-filter-card .form-select {
        border-radius: 10px;
        border: 1px solid var(--slate-200);
        padding: 9px 12px;
        font-size: 0.9rem;
    }
    .acc-filter-card .form-control:focus,
    .acc-filter-card .form-select:focus {
        border-color: var(--teal-600);
        box-shadow: 0 0 0 3px var(--teal-100);
    }

    .btn-acc-search {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 9px 12px;
        font-weight: 600;
        font-size: 0.9rem;
        color: #fff;
        background: linear-gradient(135deg, var(--teal-600), var(--teal-700));
    }

    .acc-table-card {
        background: #fff;
        border: 1px solid var(--slate-200);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .acc-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        font-size: 0.9rem;
    }

    .acc-table thead th {
        background: var(--slate-50);
        color: var(--slate-600);
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        font-weight: 700;
        padding: 13px 16px;
        border-bottom: 1px solid var(--slate-200);
        text-align: left;
        white-space: nowrap;
    }

    .acc-table tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid var(--slate-200);
        vertical-align: middle;
    }

    .acc-table tbody tr:last-child td { border-bottom: none; }
    .acc-table tbody tr { transition: background .12s ease; }
    .acc-table tbody tr:hover { background: var(--slate-50); }

    .acc-user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .acc-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        color: #fff;
        flex-shrink: 0;
    }

    .acc-avatar.role-admin { background: linear-gradient(135deg, #7c3aed, #4c1d95); }
    .acc-avatar.role-doctor { background: linear-gradient(135deg, var(--teal-600), var(--teal-900)); }
    .acc-avatar.role-receptionist { background: linear-gradient(135deg, #f59e0b, #b45309); }

    .acc-user-name {
        font-weight: 600;
        color: var(--slate-900);
        text-decoration: none;
    }
    .acc-user-name:hover { color: var(--teal-700); text-decoration: none; }

    .acc-id-pill {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 0.78rem;
        color: var(--slate-400);
    }

    .acc-role-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .acc-role-badge.role-admin { background: #f3e8ff; color: #6d28d9; }
    .acc-role-badge.role-doctor { background: var(--teal-100); color: var(--teal-900); }
    .acc-role-badge.role-receptionist { background: var(--amber-50); color: var(--amber-600); }

    .acc-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .acc-status-badge .dot { width: 7px; height: 7px; border-radius: 50%; }
    .acc-status-badge.active { background: var(--green-50); color: var(--green-600); }
    .acc-status-badge.active .dot { background: var(--green-600); }
    .acc-status-badge.locked { background: var(--red-50); color: var(--red-600); }
    .acc-status-badge.locked .dot { background: var(--red-600); }

    .acc-actions-cell {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .btn-mini {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 8px;
        padding: 6px 11px;
        font-size: 0.78rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        transition: transform .1s ease;
        white-space: nowrap;
    }
    .btn-mini:hover { transform: translateY(-1px); text-decoration: none; }

    .btn-mini-toggle-on { background: var(--red-50); color: var(--red-600); }
    .btn-mini-toggle-off { background: var(--green-50); color: var(--green-600); }
    .btn-mini-edit { background: var(--amber-50); color: var(--amber-600); }
    .btn-mini-delete { background: var(--red-50); color: var(--red-600); }
    .btn-mini-reset { background: var(--blue-50); color: var(--blue-600); }

    .acc-empty {
        text-align: center;
        padding: 48px 20px;
        color: var(--slate-400);
    }
    .acc-empty .acc-empty-icon {
        font-size: 2rem;
        margin-bottom: 10px;
        opacity: .6;
    }

    .acc-pagination {
        padding: 16px 20px;
        border-top: 1px solid var(--slate-200);
    }

    @media (max-width: 767px) {
        .acc-header { align-items: flex-start; }
        .acc-table-card { overflow-x: auto; }
        .acc-table { min-width: 780px; }
    }
</style>

<div class="container acc-page">

    <div class="acc-header">
        <div class="acc-title-block">
            <div class="acc-icon-badge">&#128101;</div>
            <div>
                <h2>Quản lý tài khoản</h2>
                <p class="acc-subtitle">Danh sách toàn bộ tài khoản trong hệ thống</p>
            </div>
        </div>

        <div class="acc-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-acc btn-acc-outline">
                ← Dashboard
            </a>

            <a href="{{ route('admin.users.create') }}" class="btn-acc btn-acc-solid">
                + Thêm tài khoản
            </a>
        </div>
    </div>

    <div class="acc-filter-card">
        <form method="GET">
            <div class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="acc-filter-label">Tìm kiếm</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Tìm tên, email, SĐT..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3">
                    <label class="acc-filter-label">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" @selected(request('role')=='admin')>Admin</option>
                        <option value="doctor" @selected(request('role')=='doctor')>Doctor</option>
                        <option value="receptionist" @selected(request('role')=='receptionist')>Receptionist</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="acc-filter-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" @selected(request('status')==='1')>Active</option>
                        <option value="0" @selected(request('status')==='0')>Locked</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn-acc-search">
                        Tìm kiếm
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div class="acc-table-card">
        <table class="acc-table">

            <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
                <th>Reset Pass</th>
            </tr>
            </thead>

            <tbody>

            @forelse($users as $user)

                <tr>

                    <td><span class="acc-id-pill">#{{ $user->id }}</span></td>

                    <td>
                        <div class="acc-user-cell">
                            <div class="acc-avatar role-{{ $user->role }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <a href="{{ route('admin.users.show', $user) }}" class="acc-user-name">
                                {{ $user->name }}
                            </a>
                        </div>
                    </td>

                    <td>{{ $user->email }}</td>

                    <td>{{ $user->phone }}</td>

                    <td>
                        <span class="acc-role-badge role-{{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    <td>
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            @if($user->is_active)
                                <span class="acc-status-badge active">
                                    <span class="dot"></span> Đang hoạt động
                                </span>
                            @else
                                <span class="acc-status-badge locked">
                                    <span class="dot"></span> Đã khóa
                                </span>
                            @endif

                            <form
                                action="{{ route('admin.users.toggle-status', $user) }}"
                                method="POST"
                                class="d-inline"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    class="btn-mini {{ $user->is_active ? 'btn-mini-toggle-on' : 'btn-mini-toggle-off' }}"
                                    onclick="return confirm('Xác nhận thay đổi trạng thái tài khoản?')"
                                >
                                    {{ $user->is_active ? 'Khóa' : 'Mở khóa' }}
                                </button>
                            </form>

                        </div>
                    </td>

                    <td>
                        <div class="acc-actions-cell">

                            <a
                                href="{{ route('admin.users.edit',$user) }}"
                                class="btn-mini btn-mini-edit"
                            >
                                Sửa
                            </a>

                            <form
                                action="{{ route('admin.users.destroy',$user) }}"
                                method="POST"
                                class="d-inline"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn-mini btn-mini-delete"
                                    onclick="return confirm('Bạn có chắc muốn xóa?')"
                                >
                                    Xóa
                                </button>
                            </form>

                        </div>
                    </td>

                    <td>
                        <form
                            action="{{ route('admin.users.reset-password', $user) }}"
                            method="POST"
                            class="d-inline"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn-mini btn-mini-reset"
                                onclick="return confirm('Đặt lại mật khẩu về 123456?')"
                            >
                                Reset Password
                            </button>
                        </form>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8">
                        <div class="acc-empty">
                            <div class="acc-empty-icon">&#128193;</div>
                            Không có dữ liệu
                        </div>
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

        <div class="acc-pagination">
            {{ $users->links() }}
        </div>
    </div>

</div>

@endsection
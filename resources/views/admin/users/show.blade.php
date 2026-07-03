@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card">

        <div class="card-header">

            <h3>Thông tin tài khoản</h3>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>ID</th>
                    <td>{{ $user->id }}</td>
                </tr>

                <tr>
                    <th>Họ tên</th>
                    <td>{{ $user->name }}</td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>

                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $user->phone }}</td>
                </tr>

                <tr>
                    <th>CCCD</th>
                    <td>{{ $user->citizen_id }}</td>
                </tr>

                <tr>
                    <th>Vai trò</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>

                <tr>
                    <th>Trạng thái</th>

                    <td>

                        @if($user->is_active)

                            <span class="badge bg-success">

                                Hoạt động

                            </span>

                        @else

                            <span class="badge bg-danger">

                                Đã khóa

                            </span>

                        @endif

                    </td>

                </tr>

                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $user->created_at }}</td>
                </tr>

                <tr>
                    <th>Cập nhật</th>
                    <td>{{ $user->updated_at }}</td>
                </tr>

            </table>

            <a
                href="{{ route('admin.users.index') }}"
                class="btn btn-secondary"
            >
                Quay lại
            </a>

        </div>

    </div>

</div>

@endsection
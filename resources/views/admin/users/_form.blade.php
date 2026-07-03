{{-- Họ và tên --}}
<div class="mb-3">
    <label class="form-label">Họ và tên</label>

    <input
        type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $user->name ?? '') }}"
    >

    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Email --}}
<div class="mb-3">
    <label class="form-label">Email</label>

    <input
        type="email"
        name="email"
        class="form-control @error('email') is-invalid @enderror"
        value="{{ old('email', $user->email ?? '') }}"
    >

    @error('email')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Số điện thoại --}}
<div class="mb-3">
    <label class="form-label">Số điện thoại</label>

    <input
        type="text"
        name="phone"
        class="form-control @error('phone') is-invalid @enderror"
        value="{{ old('phone', $user->phone ?? '') }}"
    >

    @error('phone')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- CCCD --}}
<div class="mb-3">
    <label class="form-label">CCCD</label>

    <input
        type="text"
        name="citizen_id"
        class="form-control @error('citizen_id') is-invalid @enderror"
        value="{{ old('citizen_id', $user->citizen_id ?? '') }}"
    >

    @error('citizen_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Mật khẩu --}}
<div class="mb-3">
    <label class="form-label">Mật khẩu</label>

    <input
        type="password"
        name="password"
        class="form-control @error('password') is-invalid @enderror"
    >

    @error('password')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Vai trò --}}
<div class="mb-3">
    <label class="form-label">Vai trò</label>

    <select
        name="role"
        class="form-select @error('role') is-invalid @enderror"
    >
        <option value="">-- Chọn vai trò --</option>

        <option value="admin"
            @selected(old('role', $user->role ?? '') == 'admin')>
            Admin
        </option>

        <option value="doctor"
            @selected(old('role', $user->role ?? '') == 'doctor')>
            Doctor
        </option>

        <option value="receptionist"
            @selected(old('role', $user->role ?? '') == 'receptionist')>
            Receptionist
        </option>
    </select>

    @error('role')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- Trạng thái --}}
<div class="form-check mb-4">

    <input
        class="form-check-input"
        type="checkbox"
        id="is_active"
        name="is_active"
        value="1"
        @checked(old('is_active', $user->is_active ?? true))
    >

    <label class="form-check-label" for="is_active">
        Kích hoạt tài khoản
    </label>

</div>
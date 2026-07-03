<div class="mb-3">

    <label class="form-label">
        Tên chuyên khoa
    </label>

    <input

        type="text"

        name="name"

        class="form-control @error('name') is-invalid @enderror"

        value="{{ old('name', $specialty->name ?? '') }}"

    >

    @error('name')

        <div class="invalid-feedback">

            {{ $message }}

        </div>

    @enderror

</div>

<div class="mb-3">

    <label class="form-label">

        Mô tả

    </label>

    <textarea

        name="description"

        rows="5"

        class="form-control @error('description') is-invalid @enderror"

    >{{ old('description', $specialty->description ?? '') }}</textarea>

    @error('description')

        <div class="invalid-feedback">

            {{ $message }}

        </div>

    @enderror

</div>
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount('doctors')->orderBy('id', 'desc')->get();
        return view('admin.specialties.index', compact('specialties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // E1: Kiểm tra trùng tên chuyên khoa
        $exists = Specialty::where('name', $request->name)->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'Tên chuyên khoa này đã tồn tại, vui lòng chọn tên khác.'])->withInput();
        }

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('specialties', 'public');
            $data['image'] = $path;
        }

        Specialty::create($data);

        return redirect()->route('admin.specialties.index')->with('success', 'Thêm chuyên khoa mới thành công!');
    }

    public function update(Request $request, $id)
    {
        $specialty = Specialty::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // E1: Kiểm tra trùng tên chuyên khoa (bỏ qua bản ghi hiện tại)
        $exists = Specialty::where('name', $request->name)->where('id', '!=', $id)->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'Tên chuyên khoa này đã tồn tại, vui lòng chọn tên khác.'])->withInput();
        }

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($specialty->image && Storage::disk('public')->exists($specialty->image)) {
                Storage::disk('public')->delete($specialty->image);
            }
            $path = $request->file('image')->store('specialties', 'public');
            $data['image'] = $path;
        }

        $specialty->update($data);

        return redirect()->route('admin.specialties.index')->with('success', 'Cập nhật chuyên khoa thành công!');
    }

    public function destroy($id)
    {
        $specialty = Specialty::findOrFail($id);

        // E2: Ràng buộc dữ liệu khi Xóa
        if ($specialty->doctors()->count() > 0) {
            return back()->with('error', 'Không thể xóa chuyên khoa này vì đang có Bác sĩ trực thuộc. Vui lòng chuyển các bác sĩ sang khoa khác trước khi xóa.');
        }

        // Xóa ảnh vật lý
        if ($specialty->image && Storage::disk('public')->exists($specialty->image)) {
            Storage::disk('public')->delete($specialty->image);
        }

        $specialty->delete();

        return redirect()->route('admin.specialties.index')->with('success', 'Xóa chuyên khoa thành công!');
    }
}

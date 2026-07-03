<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    /**
     * Hiển thị danh sách thuốc.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Medicine::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Sắp xếp: Ưu tiên cảnh báo hết hàng lên đầu, sau đó sắp xếp theo tên
        $medicines = $query->orderByRaw('stock < 10 DESC')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('admin.medicines.index', compact('medicines', 'search'));
    }

    /**
     * Lưu thuốc mới vào CSDL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('medicines', 'name')->whereNull('deleted_at'), // E1: Không được trùng tên
            ],
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0', // E2: Giá không được âm
            'stock' => 'required|integer|min:0', // E2: Số lượng không được âm
            'usage' => 'nullable|string|max:500',
        ], [
            'name.unique' => 'Loại thuốc này đã tồn tại trong danh mục. Vui lòng tìm kiếm và cập nhật số lượng thay vì thêm mới.',
            'price.min' => 'Đơn giá và số lượng phải lớn hơn hoặc bằng 0.',
            'stock.min' => 'Đơn giá và số lượng phải lớn hơn hoặc bằng 0.',
        ]);

        Medicine::create($request->all());

        return redirect()->route('admin.medicines.index')->with('success', 'Thêm mới loại thuốc thành công.');
    }

    /**
     * Cập nhật thông tin thuốc.
     */
    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('medicines', 'name')->ignore($medicine->id)->whereNull('deleted_at'), // Bỏ qua id hiện tại
            ],
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'usage' => 'nullable|string|max:500',
        ], [
            'name.unique' => 'Loại thuốc này đã tồn tại trong danh mục.',
            'price.min' => 'Đơn giá và số lượng phải lớn hơn hoặc bằng 0.',
            'stock.min' => 'Đơn giá và số lượng phải lớn hơn hoặc bằng 0.',
        ]);

        $medicine->update($request->all());

        return redirect()->route('admin.medicines.index')->with('success', 'Cập nhật thông tin thuốc thành công.');
    }

    /**
     * Xóa mềm (Ngừng kinh doanh).
     */
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        
        // Hướng C: Xóa mềm (vẫn giữ trong CSDL để đối soát)
        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('success', 'Đã ngừng sử dụng loại thuốc này.');
    }
}

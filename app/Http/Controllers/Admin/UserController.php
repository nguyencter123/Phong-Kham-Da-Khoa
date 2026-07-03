<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()

            ->search($request->search)

            ->role($request->role)

            ->status($request->status)

            ->latest()

            ->paginate(10)

            ->withQueryString(); // xóa trang 2 nếu không có khi search

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreUserRequest $request)
    {
        DB::transaction(function () use ($request) {

            User::create([

                'name' => $request->name,

                'email' => $request->email,

                'phone' => $request->phone,

                'citizen_id' => $request->citizen_id,

                'password' => Hash::make($request->password),

                'role' => $request->role,

                'is_active' => $request->boolean('is_active'),

            ]);

        });

        return redirect()

            ->route('admin.users.index')

            ->with('success', 'Thêm tài khoản thành công.');
    }

    /**
     * Display the specified resource.
     */
   public function show(User $user)
    {
        return view(
            'admin.users.show',
            compact('user')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view(

            'admin.users.edit',

            compact('user')

        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
    UpdateUserRequest $request,
    User $user
    )
    {
        DB::transaction(function () use ($request,$user){

            $data = $request->validated();

            if(empty($data['password']))
            {
                unset($data['password']);
            }
            else
            {
                $data['password']=Hash::make($data['password']);
            }

            $data['is_active']=$request->boolean('is_active');

            $user->update($data);

        });

        return redirect()

        ->route('admin.users.index')

        ->with(

            'success',

            'Cập nhật thành công.'

        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa chính mình.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Xóa tài khoản thành công.');
    }

     /**
     *loked 
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể khóa chính tài khoản của mình.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return back()->with(
            'success',
            $user->is_active
                ? 'Đã mở khóa tài khoản.'
                : 'Đã khóa tài khoản.'
        );
    }
    /**
     *reset pasword
     */
    public function resetPassword(User $user)
    {
        DB::transaction(function () use ($user) {

            $user->update([
                'password' => Hash::make('123456'),
            ]);

        });

        return back()->with(
            'success',
            'Đã đặt lại mật khẩu mặc định là 123456.'
        );
    }
    

}

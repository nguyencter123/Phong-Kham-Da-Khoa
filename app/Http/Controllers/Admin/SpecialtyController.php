<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Http\Requests\Admin\Specialty\StoreSpecialtyRequest;
use App\Http\Requests\Admin\Specialty\UpdateSpecialtyRequest;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $specialties = Specialty::query()

            ->when($keyword,function($query) use($keyword){

                $query->where(
                    'name',
                    'like',
                    "%{$keyword}%"
                );

            })

            ->latest()

            ->paginate(10)

            ->withQueryString();

        return view(
            'admin.specialties.index',
            compact('specialties','keyword')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('admin.specialties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecialtyRequest $request)
    {
        Specialty::create($request->validated());

        return redirect()
            ->route('admin.specialties.index')
            ->with('success', 'Thêm chuyên khoa thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

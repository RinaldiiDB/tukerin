<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBottleTypeRequest;
use App\Http\Requests\UpdateBottleTypeRequest;
use App\Models\BottleType;

class AdminBottleTypeController extends Controller
{
    /**
     * Display a listing of bottle types.
     */
    public function index()
    {
        $bottleTypes = BottleType::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.bottle_types.index', compact('bottleTypes'));
    }

    /**
     * Show the form for creating a new bottle type.
     */
    public function create()
    {
        return view('admin.bottle_types.create');
    }

    /**
     * Store a newly created bottle type in storage.
     */
    public function store(StoreBottleTypeRequest $request)
    {
        BottleType::create([
            'name'         => $request->name,
            'barcode'      => $request->barcode,
            'description'  => $request->description,
            'points_value' => $request->points_value,
        ]);

        return redirect()->route('admin.bottle-types.index')
            ->with('success', 'Jenis botol baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified bottle type.
     */
    public function edit($id)
    {
        $bottleType = BottleType::findOrFail($id);

        return view('admin.bottle_types.edit', compact('bottleType'));
    }

    /**
     * Update the specified bottle type in storage.
     */
    public function update(UpdateBottleTypeRequest $request, $id)
    {
        $bottleType = BottleType::findOrFail($id);

        $bottleType->update([
            'name'         => $request->name,
            'barcode'      => $request->barcode,
            'description'  => $request->description,
            'points_value' => $request->points_value,
        ]);

        return redirect()->route('admin.bottle-types.index')
            ->with('success', 'Data jenis botol berhasil diperbarui.');
    }

    /**
     * Remove the specified bottle type from storage.
     */
    public function destroy($id)
    {
        $bottleType = BottleType::findOrFail($id);

        // Prevent deletion if bottle type is used in any transaction detail
        if ($bottleType->details()->exists()) {
            return redirect()->route('admin.bottle-types.index')
                ->with('error', 'Jenis botol ini tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        $bottleType->delete();

        return redirect()->route('admin.bottle-types.index')
            ->with('success', 'Jenis botol berhasil dihapus.');
    }
}

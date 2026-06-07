<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminEmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = User::whereHas('role', function ($query) {
            $query->where('name', 'employee');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $employeeRole = Role::where('name', 'employee')->first();

        if (!$employeeRole) {
            return back()->with('error', 'Role Pegawai tidak ditemukan.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $employeeRole->id,
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Akun pegawai baru berhasil dibuat.');
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        return redirect()->route('admin.employees.index');
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        $employee = User::findOrFail($id);

        if (!$employee->isEmployee()) {
            return redirect()->route('admin.employees.index')->with('error', 'User tersebut bukan pegawai.');
        }

        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = User::findOrFail($id);

        if (!$employee->isEmployee()) {
            return redirect()->route('admin.employees.index')->with('error', 'User tersebut bukan pegawai.');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
    {
        $employee = User::findOrFail($id);

        if (!$employee->isEmployee()) {
            return redirect()->route('admin.employees.index')->with('error', 'User tersebut bukan pegawai.');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Akun pegawai berhasil dihapus.');
    }
}

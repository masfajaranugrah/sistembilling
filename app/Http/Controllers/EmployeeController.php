<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    // public function getDataJson()
    // {
    //     $employees = Employee::latest()->get();

    //     return response()->json([
    //         'draw' => request('draw') ?? 0,               // DataTables draw counter
    //         'recordsTotal' => $employees->count(),       // Total data sebelum filter
    //         'recordsFiltered' => $employees->count(),    // Total data setelah filter (saat ini sama)
    //         'data' => $employees,                          // Array data karyawan
    //     ]);
    // }

    // Menampilkan semua data
    public function index()
    {
        $employees = Employee::latest()->get();

        return view('content.apps.Karyawan.karyawan-list', compact('employees'));
    }

    // Form tambah
    public function create()
    {
        return view('content.apps.Karyawan.add-karyawan');
    }

    public function upload()
    {
        return view('content.apps.Karyawan.upload');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'full_address' => 'nullable|string',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'no_hp' => 'nullable|string|max:50',
            'tanggal_masuk' => 'nullable|date',
            'jabatan' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:50',
            'atas_nama' => 'nullable|string|max:255',
        ]);

        Employee::create($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    // Form edit
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        return view('content.apps.Karyawan.karyawan-edit', compact('employee'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $employee->update([
            'nik' => $request->nip,
            'full_name' => $request->full_name,
            'full_address' => $request->full_address,
            'place_of_birth' => $request->place_of_birth,
            'date_of_birth' => $request->date_of_birth,
            'no_hp' => $request->no_hp,
            'tanggal_masuk' => $request->tanggal_masuk,
            'jabatan' => $request->jabatan,
            'bank' => $request->bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama' => $request->atas_nama,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    // Hapus data
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new EmployeeImport, $request->file('file'));

        return redirect()->route('karyawan.index')->with('success', '✅ Data Excel berhasil diimport!');
    }
}

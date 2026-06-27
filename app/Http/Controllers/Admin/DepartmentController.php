<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with('faculty')->orderBy('name');

        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $departments = $query->paginate(20)->withQueryString();
        $faculties = Faculty::orderBy('name')->get();
        $faculty = $request->filled('faculty_id')
            ? Faculty::find($request->faculty_id)
            : null;

        return view('admin.department.index', compact('departments', 'faculties', 'faculty'));
    }

    public function create(Request $request)
    {
        $faculties = Faculty::orderBy('name')->get();
        $selectedFacultyId = $request->query('faculty_id');
        return view('admin.department.create', compact('faculties', 'selectedFacultyId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name'       => 'required|string|max:100',
            'code'       => 'required|string|max:20|unique:departments,code',
            'level'      => 'nullable|array',
            'level.*'    => 'in:S1,S2,S3,D IV,PROFESI',
        ], [
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'faculty_id.exists'   => 'Fakultas tidak valid.',
            'name.required'       => 'Nama jurusan wajib diisi.',
            'code.required'       => 'Kode jurusan wajib diisi.',
            'code.unique'         => 'Kode jurusan sudah digunakan.',
            'code.max'            => 'Kode jurusan maksimal 20 karakter.',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['level'] = isset($validated['level'])
            ? implode(',', $validated['level'])
            : null;

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', "Jurusan \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function edit(Department $department)
    {
        $faculties = Faculty::orderBy('name')->get();
        $selectedLevels = $department->level ? explode(',', $department->level) : [];
        return view('admin.department.edit', compact('department', 'faculties', 'selectedLevels'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name'       => 'required|string|max:100',
            'code'       => "required|string|max:20|unique:departments,code,{$department->id}",
            'level'      => 'nullable|array',
            'level.*'    => 'in:S1,S2,S3,D IV,PROFESI',
        ], [
            'faculty_id.required' => 'Fakultas wajib dipilih.',
            'faculty_id.exists'   => 'Fakultas tidak valid.',
            'name.required'       => 'Nama jurusan wajib diisi.',
            'code.required'       => 'Kode jurusan wajib diisi.',
            'code.unique'         => 'Kode jurusan sudah digunakan.',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['level'] = isset($validated['level'])
            ? implode(',', $validated['level'])
            : null;

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', "Jurusan \"{$department->name}\" berhasil diperbarui.");
    }

    public function destroy(Department $department)
    {
        $name = $department->name;
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => "Jurusan \"{$name}\" berhasil dihapus.",
        ]);
    }
}

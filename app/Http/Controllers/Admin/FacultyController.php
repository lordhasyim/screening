<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::withCount('departments')->orderBy('name')->paginate(20);
        $totalFaculties = Faculty::count();
        $totalDepartments = Department::count();

        return view('admin.faculty.index', compact('faculties', 'totalFaculties', 'totalDepartments'));
    }

    public function create()
    {
        return view('admin.faculty.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:faculties,code',
        ], [
            'name.required' => 'Nama fakultas wajib diisi.',
            'code.required' => 'Kode fakultas wajib diisi.',
            'code.unique'   => 'Kode fakultas sudah digunakan.',
            'code.max'      => 'Kode fakultas maksimal 10 karakter.',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        Faculty::create($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', "Fakultas \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function show(Faculty $faculty)
    {
        $departments = $faculty->departments()->orderBy('name')->get();
        $faculty->loadCount(['departments', 'quizResponses']);
        return view('admin.faculty.show', compact('faculty', 'departments'));
    }

    public function edit(Faculty $faculty)
    {
        $faculty->loadCount('departments');
        return view('admin.faculty.edit', compact('faculty'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => "required|string|max:10|unique:faculties,code,{$faculty->id}",
        ], [
            'name.required' => 'Nama fakultas wajib diisi.',
            'code.required' => 'Kode fakultas wajib diisi.',
            'code.unique'   => 'Kode fakultas sudah digunakan.',
            'code.max'      => 'Kode fakultas maksimal 10 karakter.',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $faculty->update($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', "Fakultas \"{$faculty->name}\" berhasil diperbarui.");
    }

    public function destroy(Faculty $faculty)
    {
        $name = $faculty->name;
        $deptCount = $faculty->departments()->count();
        $faculty->delete();

        return response()->json([
            'success' => true,
            'message' => "Fakultas \"{$name}\" dan {$deptCount} jurusan terkait berhasil dihapus.",
        ]);
    }

    public function departments(Faculty $faculty)
    {
        $departments = $faculty->departments()->orderBy('name')->paginate(20);
        return view('admin.department.index', compact('departments', 'faculty'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // 1. Fungsi Index dengan Fitur Search
    public function index(Request $request) 
    {
        $query = DB::table('students')
            ->join('platforms', 'students.asal_platform_id', '=', 'platforms.id')
            ->select('students.*', 'platforms.sumber as platform_name');

        // Fitur Search

    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;
        // Memecah kata kunci berdasarkan spasi agar lebih fleksibel
        $words = explode(' ', $searchTerm);
        
        $query->where(function($q) use ($words) {
            foreach ($words as $word) {
                $q->orWhere('students.nama_lengkap', 'ILIKE', '%' . $word . '%')
                  ->orWhere('platforms.sumber', 'ILIKE', '%' . $word . '%')
                  ->orWhere('students.status', 'ILIKE', '%' . $word . '%')
                  ->orWhere('students.email', 'ILIKE', '%' . $word . '%');
            }
        });
    }

    $students = $query->orderBy('students.created_at', 'desc')->get();

    // Jika ini adalah permintaan AJAX (Live Search), kirim JSON
    if ($request->ajax()) {
        return response()->json($students);
    }
            
    return view('dashboard.students_index', compact('students'));
    }

    // 2. Fungsi Create (Hanya menampilkan form)
    public function create() 
    {
        $platforms = DB::table('platforms')->get();
        return view('dashboard.add_student', compact('platforms'));
    }

    // 3. Fungsi Store (Simpan data mahasiswa baru)
    public function store(Request $request) 
    {
        $request->validate([
            'nama_lengkap'     => 'required|string|max:255',
            'email'            => 'required|email|unique:students,email',
            'asal_platform_id' => 'required|exists:platforms,id',
            'status'           => 'required',
        ], [
            'email.unique'     => 'Email ini sudah terdaftar sebelumnya.',
        ]);

        DB::transaction(function () use ($request) {
            DB::table('students')->insert([
                'nama_lengkap'     => $request->nama_lengkap,
                'email'            => $request->email,
                'asal_platform_id' => $request->asal_platform_id,
                'status'           => $request->status,
                'created_at'       => now()
            ]);

            DB::table('platforms')->where('id', $request->asal_platform_id)->increment('visitor');

            $targetColumns = $this->getCumulativeColumns($request->status);
            foreach ($targetColumns as $column) {
                DB::table('platforms')->where('id', $request->asal_platform_id)->increment($column);
            }
        });

        return redirect()->route('students.index')->with('success', 'Mahasiswa dan statistik visitor berhasil diperbarui!');
    }

    // 4. Fungsi Destroy
    public function destroy($id)
    {
        $student = DB::table('students')->where('id', $id)->first();

        if ($student) {
            DB::transaction(function () use ($student, $id) {
                DB::table('platforms')->where('id', $student->asal_platform_id)->decrement('visitor');

                $columnsToDecrement = $this->getCumulativeColumns($student->status);
                foreach ($columnsToDecrement as $col) {
                    DB::table('platforms')->where('id', $student->asal_platform_id)->decrement($col);
                }

                DB::table('students')->where('id', $id)->delete();
            });
        }
        return redirect()->route('students.index')->with('success', 'Data mahasiswa berhasil dihapus!');
    }

    // 5. Fungsi Edit
    public function edit($id) {
        $student = DB::table('students')->where('id', $id)->first();
        $platforms = DB::table('platforms')->get();
        return view('dashboard.students_edit', compact('student', 'platforms'));
    }

    // 6. Fungsi Update
    public function update(Request $request, $id) {
        $student = DB::table('students')->where('id', $id)->first();

        DB::transaction(function () use ($request, $student, $id) {
            // Update statistik jika status atau platform berubah
            if ($student->status != $request->status || $student->asal_platform_id != $request->asal_platform_id) {
                // Kurangi dari data lama
                $oldColumns = $this->getCumulativeColumns($student->status);
                DB::table('platforms')->where('id', $student->asal_platform_id)->decrement('visitor');
                foreach ($oldColumns as $col) {
                    DB::table('platforms')->where('id', $student->asal_platform_id)->decrement($col);
                }

                // Tambah ke data baru
                $newColumns = $this->getCumulativeColumns($request->status);
                DB::table('platforms')->where('id', $request->asal_platform_id)->increment('visitor');
                foreach ($newColumns as $col) {
                    DB::table('platforms')->where('id', $request->asal_platform_id)->increment($col);
                }
            }

            DB::table('students')->where('id', $id)->update([
                'nama_lengkap'     => $request->nama_lengkap,
                'email'            => $request->email,
                'asal_platform_id' => $request->asal_platform_id,
                'status'           => $request->status,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Data berhasil diperbarui!');
    }

    // Helper
    private function getCumulativeColumns($status) {
        return match($status) {
            'Enrolled'       => ['lead', 'exam_taker', 'registered', 'enrolled'],
            'Registered'     => ['lead', 'exam_taker', 'registered'],
            'Test/Interview' => ['lead', 'exam_taker'],
            'Lead'           => ['lead'],
            default          => []
        };
    }
}
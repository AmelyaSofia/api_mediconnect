<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class DoctorController extends Controller
{
    /**
     * GET - List semua dokter (USER & ADMIN)
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Doctor::orderBy('created_at', 'desc')->get()
        ], 200);
    }

    /**
     * GET - Detail dokter by ID (USER & ADMIN)
     */
    public function show($id)
    {
        try {
            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'message' => 'Dokter tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'message' => 'Detail dokter',
                'data' => $doctor
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST - Tambah dokter (ADMIN ONLY)
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Forbidden, hanya admin yang boleh menambah dokter'
                ], 403);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'specialization' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'schedule' => 'required|string',
                'description' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $fileName = time().'_'.$request->file('photo')->getClientOriginalName();

                $request->file('photo')->move(
                    public_path('doctors'),
                    $fileName
                );

                $photoPath = 'doctors/' . $fileName; 
            }


            $doctor = Doctor::create([
                'name' => $request->name,
                'specialization' => $request->specialization,
                'phone' => $request->phone,
                'schedule' => $request->schedule,
                'description' => $request->description,
                'photo' => $photoPath
            ]);

            return response()->json([
                'message' => 'Dokter berhasil ditambahkan',
                'data' => $doctor
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT - Update dokter (ADMIN ONLY)
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Forbidden, hanya admin yang boleh mengubah data dokter'
                ], 403);
            }

            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'message' => 'Dokter tidak ditemukan'
                ], 404);
            }

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'specialization' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'schedule' => 'sometimes|string',
                'description' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            if ($request->hasFile('photo')) {

                if ($doctor->photo && file_exists(public_path($doctor->photo))) {
                    unlink(public_path($doctor->photo));
                }

                $fileName = time().'_'.$request->file('photo')->getClientOriginalName();

                $request->file('photo')->move(
                    public_path('doctors'),
                    $fileName
                );

                $doctor->photo = 'doctors/' . $fileName;
            }

            $doctor->update($request->only([
                'name',
                'specialization',
                'phone',
                'schedule',
                'description'
            ]));

            return response()->json([
                'message' => 'Data dokter berhasil diperbarui',
                'data' => $doctor
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * DELETE - Hapus dokter (ADMIN ONLY)
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Forbidden, hanya admin yang boleh menghapus dokter'
                ], 403);
            }

            $doctor = Doctor::find($id);

            if (!$doctor) {
                return response()->json([
                    'message' => 'Dokter tidak ditemukan'
                ], 404);
            }

            if ($doctor->photo && file_exists(public_path($doctor->photo))) {
                unlink(public_path($doctor->photo));
            }

            $doctor->delete();

            return response()->json([
                'message' => 'Dokter berhasil dihapus'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
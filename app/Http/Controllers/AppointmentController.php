<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AppointmentController extends Controller
{
    /**
     * =========================
     * USER - Buat Janji Temu
     * =========================
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'appointment_date' => 'required|date',
                'appointment_time' => 'required',
                'note' => 'nullable|string'
            ]);

            $appointment = Appointment::create([
                'user_id' => Auth::id(),
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'note' => $request->note,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Janji temu berhasil dibuat',
                'data' => $appointment
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ==================================
     * USER - Lihat Janji Temu Miliknya
     * ==================================
     */
    public function myAppointments()
    {
        try {
            $appointments = Appointment::with('doctor')
                ->where('user_id', Auth::id())
                ->orderBy('appointment_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $appointments
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ==================================
     * ADMIN - Lihat Semua Janji Temu
     * ==================================
     */
    public function adminIndex()
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Forbidden, hanya admin yang boleh mengakses'
                ], 403);
            }

            $appointments = Appointment::with(['user', 'doctor'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $appointments
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ==================================
     * ADMIN - Setujui / Tolak Janji Temu
     * ==================================
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'message' => 'Forbidden, hanya admin yang boleh mengubah status'
                ], 403);
            }

            $request->validate([
                'status' => 'required|in:approved,rejected'
            ]);

            $appointment = Appointment::find($id);

            if (!$appointment) {
                return response()->json([
                    'message' => 'Janji temu tidak ditemukan'
                ], 404);
            }

            $appointment->status = $request->status;
            $appointment->save();

            return response()->json([
                'success' => true,
                'message' => 'Status janji temu berhasil diperbarui',
                'data' => $appointment
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

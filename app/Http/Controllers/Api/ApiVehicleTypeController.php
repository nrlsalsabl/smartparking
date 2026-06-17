<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiVehicleTypeController extends Controller
{
    /**
     * Tampilkan semua data tipe kendaraan (JSON)
     */
    public function index()
    {
        // Menggunakan get() atau paginate() tergantung kebutuhan mobile app Anda
        $vehicleTypes = VehicleType::latest()->get(); 

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil semua data tipe kendaraan',
            'data'    => $vehicleTypes
        ], 200);
    }

    /**
     * Simpan data tipe kendaraan baru via API
     */
    public function store(Request $request)
    {
        // Validasi input khusus API agar error mengembalikan JSON (bukan redirect back)
        $validator = Validator::make($request->all(), [
            'type_name'      => 'required|string|max:255',
            'price_per_hour' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $vehicleType = VehicleType::create([
            'type_name'      => $request->type_name,
            'price_per_hour' => $request->price_per_hour
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tipe kendaraan berhasil ditambahkan',
            'data'    => $vehicleType
        ], 201);
    }

    /**
     * Tampilkan detail satu data tipe kendaraan berdasarkan ID
     */
    public function show(string $id)
    {
        $vehicleType = VehicleType::find($id);

        if (!$vehicleType) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tipe kendaraan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail data tipe kendaraan berhasil ditemukan',
            'data'    => $vehicleType
        ], 200);
    }

    /**
     * Update data tipe kendaraan via API
     */
    public function update(Request $request, string $id)
    {
        $vehicleType = VehicleType::find($id);

        if (!$vehicleType) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan untuk diupdate'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type_name'      => 'required|string|max:255',
            'price_per_hour' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $vehicleType->update([
            'type_name'      => $request->type_name,
            'price_per_hour' => $request->price_per_hour
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Tipe kendaraan berhasil diupdate',
            'data'    => $vehicleType
        ], 200);
    }

    /**
     * Hapus data tipe kendaraan via API
     */
    public function destroy(string $id)
    {
        $vehicleType = VehicleType::find($id);

        if (!$vehicleType) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan atau sudah dihapus'
            ], 404);
        }

        $vehicleType->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Tipe kendaraan berhasil dihapus'
        ], 200);
    }
}
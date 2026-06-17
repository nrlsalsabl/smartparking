<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParkingArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiParkingAreaController extends Controller
{
    /**
     * Tampilkan semua data area parkir (Khusus Admin)
     */
    public function index()
    {
        $parkingAreas = ParkingArea::latest()->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil semua data area parkir',
            'data'    => $parkingAreas
        ], 200);
    }

    /**
     * Simpan data area parkir baru (Khusus Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_name' => 'required|string|max:255',
            'capacity'  => 'required|numeric',
            'location'  => 'nullable|string',
            'description'=> 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $parkingArea = ParkingArea::create([
            'area_name'   => $request->area_name,
            'capacity'    => $request->capacity,
            'location'    => $request->location,
            'description' => $request->description,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Area parkir berhasil ditambahkan',
            'data'    => $parkingArea
        ], 201);
    }

    /**
     * Tampilkan detail satu area parkir berdasarkan ID (Khusus Admin)
     */
    public function show(string $id)
    {
        $parkingArea = ParkingArea::find($id);

        if (!$parkingArea) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data area parkir tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail area parkir ditemukan',
            'data'    => $parkingArea
        ], 200);
    }

    /**
     * Update data area parkir (Khusus Admin)
     */
    public function update(Request $request, string $id)
    {
        $parkingArea = ParkingArea::find($id);

        if (!$parkingArea) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data area parkir tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'area_name' => 'required|string|max:255',
            'capacity'  => 'required|numeric',
            'location'  => 'nullable|string',
            'description'=> 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $parkingArea->update([
            'area_name'   => $request->area_name,
            'capacity'    => $request->capacity,
            'location'    => $request->location,
            'description' => $request->description,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Area parkir berhasil diperbarui',
            'data'    => $parkingArea
        ], 200);
    }

    /**
     * Hapus data area parkir (Khusus Admin)
     */
    public function destroy(string $id)
    {
        $parkingArea = ParkingArea::find($id);

        if (!$parkingArea) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data area parkir tidak ditemukan atau sudah dihapus'
            ], 404);
        }

        $parkingArea->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Area parkir berhasil dihapus'
        ], 200);
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiVehicleController extends Controller
{
    /**
     * Tampilkan daftar kendaraan (Fleksibel sesuai Role + Fitur Search)
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $search = $request->search;

        // Memulai query dasar dengan relasi tipe kendaraan
        $query = Vehicle::with('vehicleType');

        // JIKA BUKAN ADMIN (Misal Customer), batasi hanya kendaraan miliknya sendiri
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Fitur Pencarian berdasarkan nomor plat kendaraan
        $query->when($search, function ($q) use ($search) {
            $q->where('plate_number', 'like', '%' . $search . '%');
        });

        $vehicles = $query->latest()->get();

        return response()->json([
            'status'     => 'success',
            'message'    => 'Berhasil mengambil data kendaraan',
            'role_akses' => $user->role->role_name,
            'data'       => $vehicles
        ], 200);
    }

    /**
     * Simpan data kendaraan baru (Otomatis terikat ke User ID yang login)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'plate_number'    => 'required|string|unique:vehicles,plate_number',
            'brand'           => 'required|string',
            'color'           => 'required|string',
            'vehicle_photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $photo = null;
        if ($request->hasFile('vehicle_photo')) {
            $photo = $request->file('vehicle_photo')->store('vehicles', 'public');
        }

        $vehicle = Vehicle::create([
            'user_id'         => auth('api')->id(), // Mengikat otomatis ke ID JWT Token
            'vehicle_type_id' => $request->vehicle_type_id,
            'plate_number'    => strtoupper($request->plate_number),
            'brand'           => $request->brand,
            'color'           => $request->color,
            'vehicle_photo'   => $photo,
            'status'          => 'active'
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kendaraan berhasil ditambahkan',
            'data'    => $vehicle->load('vehicleType')
        ], 201);
    }

    /**
     * Tampilkan detail satu kendaraan (Fleksibel sesuai Role)
     */
    public function show(string $id)
    {
        $user = auth('api')->user();
        $query = Vehicle::with('vehicleType');

        // Jika bukan admin, hanya boleh melihat detail kendaraan miliknya sendiri
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $vehicle = $query->find($id);

        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data kendaraan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail kendaraan ditemukan.',
            'data'    => $vehicle
        ], 200);
    }

    /**
     * Update data kendaraan (Fleksibel sesuai Role)
     */
    public function update(Request $request, string $id)
    {
        $user = auth('api')->user();
        $query = Vehicle::query();

        // Batasi target update jika bukan admin
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $vehicle = $query->find($id);

        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data kendaraan tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'plate_number'    => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'brand'           => 'required|string',
            'color'           => 'required|string',
            'vehicle_photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $photo = $vehicle->vehicle_photo;

        if ($request->hasFile('vehicle_photo')) {
            // Hapus foto lama jika ada
            if ($photo) {
                Storage::disk('public')->delete($photo);
            }
            $photo = $request->file('vehicle_photo')->store('vehicles', 'public');
        }

        $vehicle->update([
            'vehicle_type_id' => $request->vehicle_type_id,
            'plate_number'    => strtoupper($request->plate_number),
            'brand'           => $request->brand,
            'color'           => $request->color,
            'vehicle_photo'   => $photo,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kendaraan berhasil diperbarui',
            'data'    => $vehicle->load('vehicleType')
        ], 200);
    }

    /**
     * Hapus data kendaraan beserta fotonya (Fleksibel sesuai Role)
     */
    public function destroy(string $id)
    {
        $user = auth('api')->user();
        $query = Vehicle::query();

        // Batasi hak hapus jika bukan admin
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $vehicle = $query->find($id);

        if (!$vehicle) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses untuk menghapusnya.'
            ], 404);
        }

        // Hapus file foto dari storage lokal/public
        if ($vehicle->vehicle_photo) {
            Storage::disk('public')->delete($vehicle->vehicle_photo);
        }

        $vehicle->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kendaraan berhasil dihapus.'
        ], 200);
    }
}
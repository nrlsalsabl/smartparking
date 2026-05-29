<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $vehicles = Vehicle::where(
            'user_id',
            auth()->id()
        )

        ->when($search, function ($query) use ($search) {

            $query->where(
                'plate_number',
                'like',
                '%' . $search . '%'
            );

        })

        ->latest()
        ->paginate(10);

        return view(
            'vehicles.index',
            compact(
                'vehicles',
                'search'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicleTypes = VehicleType::all();

        return view(
            'vehicles.create',
            compact('vehicleTypes')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'vehicle_type_id' => 'required',

            'plate_number' => 'required|unique:vehicles',

            'brand' => 'required',

            'color' => 'required',

            'vehicle_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $photo = null;

        if ($request->hasFile('vehicle_photo')) {

            $photo = $request
                ->file('vehicle_photo')
                ->store('vehicles', 'public');
        }

        Vehicle::create([

            'user_id' => auth()->id(),

            'vehicle_type_id' => $request->vehicle_type_id,

            'plate_number' => strtoupper($request->plate_number),

            'brand' => $request->brand,

            'color' => $request->color,

            'vehicle_photo' => $photo,

            'status' => 'active'

        ]);

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Vehicle created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vehicle = Vehicle::where('user_id',auth()->id())->findOrFail($id);

        $vehicleTypes = VehicleType::all();

        return view(
            'vehicles.edit',
            compact(
                'vehicle',
                'vehicleTypes'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([

            'vehicle_type_id' => 'required',

            'plate_number' => 'required|unique:vehicles,plate_number,' . $vehicle->id,

            'brand' => 'required',

            'color' => 'required',

            'vehicle_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $photo = $vehicle->vehicle_photo;

        if ($request->hasFile('vehicle_photo')) {

            if ($photo) {
                Storage::disk('public')->delete($photo);
            }

            $photo = $request
                ->file('vehicle_photo')
                ->store('vehicles', 'public');
        }

        $vehicle->update([

            'vehicle_type_id' => $request->vehicle_type_id,

            'plate_number' => strtoupper($request->plate_number),

            'brand' => $request->brand,

            'color' => $request->color,

            'vehicle_photo' => $photo,

        ]);

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Vehicle updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($vehicle->vehicle_photo) {

            Storage::disk('public')
                ->delete($vehicle->vehicle_photo);
        }

        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Vehicle deleted');
    }
}

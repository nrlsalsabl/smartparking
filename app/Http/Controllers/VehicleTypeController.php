<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicleTypes = VehicleType::latest()->paginate(10);

        return view(
            'vehicle-types.index',
            compact('vehicleTypes')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicle-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required',
            'price_per_hour' => 'required|numeric'
        ]);

        VehicleType::create([
            'type_name' => $request->type_name,
            'price_per_hour' => $request->price_per_hour
        ]);

        return redirect()
            ->route('vehicle-types.index')
            ->with('success', 'Vehicle type created');
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
        $vehicleType = VehicleType::findOrFail($id);

        return view(
            'vehicle-types.edit',
            compact('vehicleType')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'type_name' => 'required',
            'price_per_hour' => 'required|numeric'
        ]);

        $vehicleType = VehicleType::findOrFail($id);

        $vehicleType->update([
            'type_name' => $request->type_name,
            'price_per_hour' => $request->price_per_hour
        ]);

        return redirect()
            ->route('vehicle-types.index')
            ->with('success', 'Vehicle type updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicleType = VehicleType::findOrFail($id);

        $vehicleType->delete();

        return redirect()
            ->route('vehicle-types.index')
            ->with('success', 'Vehicle type deleted');
    }
}

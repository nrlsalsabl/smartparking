<?php

namespace App\Http\Controllers;

use App\Models\ParkingArea;
use Illuminate\Http\Request;

class ParkingAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parkingAreas = ParkingArea::latest()
            ->paginate(10);

        return view(
            'parking-areas.index',
            compact('parkingAreas')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('parking-areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required',
            'capacity' => 'required|numeric',
        ]);

        ParkingArea::create([
            'area_name' => $request->area_name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('parking-areas.index')
            ->with('success', 'Parking area created');
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
        $parkingArea = ParkingArea::findOrFail($id);

        return view(
            'parking-areas.edit',
            compact('parkingArea')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'area_name' => 'required',
            'capacity' => 'required|numeric',
        ]);

        $parkingArea = ParkingArea::findOrFail($id);

        $parkingArea->update([
            'area_name' => $request->area_name,
            'capacity' => $request->capacity,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('parking-areas.index')
            ->with('success', 'Parking area updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parkingArea = ParkingArea::findOrFail($id);

        $parkingArea->delete();

        return redirect()
            ->route('parking-areas.index')
            ->with('success', 'Parking area deleted');
    }
}

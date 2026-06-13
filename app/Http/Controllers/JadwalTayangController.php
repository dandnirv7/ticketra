<?php

namespace App\Http\Controllers;

use App\Models\JadwalTayang;
use Illuminate\Http\Request;

class JadwalTayangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwals = JadwalTayang::with(['film', 'studio.bioskop'])
            ->where('status', 'terjadwal')
            ->orderBy('waktu_mulai', 'asc')
            ->paginate(10);

        return view('jadwal.index', compact('jadwals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalTayang $jadwalTayang)
    {
        $jadwalTayang->load([
            'film',
            'studio.bioskop',
        ]);

        return view('jadwal.show', compact('jadwalTayang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalTayang $jadwalTayang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalTayang $jadwalTayang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalTayang $jadwalTayang)
    {
        //
    }
}

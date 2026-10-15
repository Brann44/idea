<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStepRequest;
use App\Models\Step;
use Illuminate\Support\Facades\Gate;

class StepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreStepRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Step $step)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Step $step)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Step $step)
    {

        Gate::authorize('update', $step);

        $step->update([
            'completed' => request()->boolean('completed'),
        ]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Step $step)
    {
        //
    }
}

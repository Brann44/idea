<?php

namespace App\Http\Controllers;

use App\Actions\CreateIdea;
use App\Actions\UpdateIdea;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->status;
        if (! in_array($status, IdeaStatus::values())) {
            $status = null;
        }
        $ideas = $user
            ->ideas()
            ->latest() // <-- Para mostrar las más recientes primero
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->get();

        return view('idea.index', [
            'ideas' => $ideas,
            'statusCounts' => Idea::statusCounts($user),
        ]);
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
    public function store(StoreIdeaRequest $request, CreateIdea $createIdea)
    {
        $createIdea->handle($request->validated());

        return redirect()->route('idea.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        Gate::authorize('view', $idea);

        return view('idea.show', [
            'idea' => $idea,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea, UpdateIdea $updateIdea)
    {
        Gate::authorize('update', $idea);
        $updateIdea->handle($idea, $request->validated());

        return redirect()->route('idea.show', $idea);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        Gate::authorize('delete', $idea);

        $idea->delete();

        return redirect()->route('idea.index');
    }
}

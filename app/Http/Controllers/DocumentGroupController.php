<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentGroupRequest;
use App\Http\Requests\UpdateDocumentGroupRequest;
use App\Models\DocumentGroup;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DocumentGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = Request::input('filter');
        if (!is_string($filter)) {
            $filter = '';
        }

        $groups = DocumentGroup::when($filter, function($query) use ($filter) {
            $query->where('name', 'like', "%{$filter}%");
        })
            ->withCount('documents')
            ->paginate();

        return Inertia::render('DocumentGroup/List', [
            'groups' => $groups,
            'filters' => [
                'filter' => $filter,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('DocumentGroup/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentGroupRequest $request)
    {
        dd($request->validated());
        
        $documentGroup = DocumentGroup::create($request->validated());

        if ($documentGroup === null) {
            return response()->json(["Σφάλμα δημιουργίας νέας ομάδας εγγράφων"], 401);
        }

        return response()->json($documentGroup);
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentGroup $documentGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentGroup $documentGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentGroupRequest $request, DocumentGroup $documentGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentGroup $documentGroup)
    {
        //
    }
}

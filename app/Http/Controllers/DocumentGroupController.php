<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentGroupRequest;
use App\Http\Requests\UpdateDocumentGroupRequest;
use App\Jobs\AddQRToDocuments;
use App\Jobs\ZipDocumentGroup;
use App\Models\Document;
use App\Models\DocumentGroup;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DocumentGroupController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(DocumentGroup::class, 'documentGroup');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filter = Request::input('filter');
        if (! is_string($filter)) {
            $filter = '';
        }

        $groups = DocumentGroup::when($filter, function ($query) use ($filter) {
            $query->where('name', 'like', "%{$filter}%");
        })
            ->withCount('documents')
            ->paginate();

        return Inertia::render('DocumentGroup/List', [
            'groups' => $groups,
            'filters' => [
                'filter' => $filter,
            ],
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
        $userId = auth()->user()->getAuthIdentifier();

        $validated = $request->validated();

        // Αλλαγή βάσης μέτρησης από 0-4 σε 1-5
        $validated['step']++;

        $documentGroup = DocumentGroup::create(array_merge($validated, [
            'user_id' => $userId,
        ]));

        if ($documentGroup === null) {
            return response()->json(['Σφάλμα δημιουργίας νέας ομάδας εγγράφων'], 401);
        }

        return response()->json($documentGroup);
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentGroup $documentGroup)
    {
        $documentGroup->load('documents');

        return response()->json($documentGroup);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DocumentGroup $documentGroup)
    {
        $documentGroup->load('documents');

        return Inertia::render('DocumentGroup/Edit', [
            'group' => $documentGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentGroupRequest $request, DocumentGroup $documentGroup)
    {
        $documentGroup->update(array_merge($request->validated(), [
            'updated_at' => now(),
        ]));

        // Αλλαγή βάσης μέτρησης από 0-4 σε 1-5
        $documentGroup->step++;

        $documentGroup->save();

        return response()->json($documentGroup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentGroup $documentGroup)
    {
        //
    }

    public function addQR(DocumentGroup $documentGroup)
    {
        logger("Adding QR to group: {$documentGroup->name}");

        $documentGroup->job_status = DocumentGroup::JobInProgress;
        $documentGroup->job_status_text = 'Προσθήκη QR στην ομάδα';
        $documentGroup->job_start_date = now();

        $documentGroup->save();

        Bus::chain([
            new AddQRToDocuments($documentGroup),
            new ZipDocumentGroup($documentGroup, Document::WithQR),
        ])->dispatch();

        return response()->json('OK');
    }

    public function getQR(DocumentGroup $documentGroup)
    {
        return response()->download(
            storage_path('app') . "/" . $documentGroup->id . "/qr/" . $documentGroup->id . ".zip"
        );
    }

    public function togglePublished(DocumentGroup $documentGroup)
    {
        $documentGroup->published =! $documentGroup->published;
        $documentGroup->save();

        return response()->json($documentGroup);
    }
}

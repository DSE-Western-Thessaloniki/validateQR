<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\StoreManyDocumentsRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;

class DocumentController extends Controller
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
    public function store(StoreDocumentRequest $request)
    {
        $document = Document::create($request->validated());

        return response()->json($document);
    }

    public function storeMany(StoreManyDocumentsRequest $request)
    {
        $validated = $request->validated();

        $documents = [];

        if ($request->file('documents')) {
            foreach($request->file('documents') as $file) {
                $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file->getClientOriginalName());
                // Remove any runs of periods
                $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

                $document = Document::create([
                    'document_group_id' => $validated['document_group_id'],
                    'filename' => $filename,
                    'state' => 0,
                ]);

                $file->storeAs($validated['document_group_id'], "{$document->id}.pdf");

                $documents[] = $document;
            }
        }

        return response()->json($documents);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        //
    }
}

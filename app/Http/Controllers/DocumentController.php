<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\StoreManyDocumentsRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{

    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        // $this->authorizeResource(Document::class, 'document');
    }

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
        Gate::authorize("create", Document::class);

        $document = Document::create($request->validated());

        return response()->json($document);
    }

    public function storeMany(StoreManyDocumentsRequest $request)
    {
        Gate::authorize("create", Document::class);

        $validated = $request->validated();

        $documents = [];

        $documentGroup = DocumentGroup::findOrFail($validated['document_group_id']);

        if ($request->file('documents')) {
            foreach ($request->file('documents') as $file) {
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

        $documentGroup->step++;
        $documentGroup->save();

        return response()->json($documents);
    }

    public function storeManySigned(StoreManyDocumentsRequest $request)
    {
        Gate::authorize("create", Document::class);

        $validated = $request->validated();

        $documents = [];

        $documentGroup = DocumentGroup::findOrFail($validated['document_group_id']);

        if ($request->file('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file->getClientOriginalName());
                // Remove any runs of periods
                $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

                $document = Document::where('document_group_id', $validated['document_group_id'])
                    ->where(function (Builder $query) use ($filename) {
                        $query->where('filename', $filename)
                            ->orWhere('filename', str_replace('_signed', '', $filename));
                    })
                    ->first();

                // Αν δεν έχει ανέβει ήδη το σχετικό έγγραφο απλά αγνόησέ το
                if (!$document) {
                    continue;
                }

                $file->storeAs($validated['document_group_id'] . '/signed/', "{$document->id}.pdf");

                $document->state = Document::WithQRAndSignature;
                $document->save();


                $documents[] = $document;
            }
        }

        $documentsWithoutSignature = Document::where('document_group_id', $validated['document_group_id'])
            ->where('state', '<>', Document::WithQRAndSignature)
            ->count();

        // Αν έχουμε υπογραφές για όλα τα έγγραφα, αύξησε το βήμα
        if ($documentsWithoutSignature === 0) {
            $documentGroup->step++;
            $documentGroup->save();
        }

        return response()->json($documents);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        // Έλεγξε μήπως δεν έχει πάρει ψηφιακή υπογραφή ή το group δεν είναι δημοσιευμένο
        if (
            !file_exists(storage_path("app"). "/{$document->document_group_id}/signed/{$document->id}.pdf") ||
            !$document->documentGroup->published
            ) {
            Log::info("Document with id '{$document->id}' not found or group not published. [{$_SERVER['HTTP_X_FORWARDED_FOR']} -> {$_SERVER['REMOTE_ADDR']}]");
            return inertia('Error/DocumentNotFound');
        }

        return response()->download(
            storage_path("app"). "/{$document->document_group_id}/signed/{$document->id}.pdf",
            "{$document->filename}"
        );
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\StoreManyDocumentsRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentExtraState;
use App\Models\DocumentGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

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
        $filter = Request::input('filter');
        if (! is_string($filter)) {
            $filter = '';
        }
        $cancelled = Request::input('cancelled', "false") === "true" ? true : false;

        $replaced = Request::input('replaced', "false") === "true" ? true : false;

        $documents = Document::query()
            ->when($filter, function ($query) use ($filter) {
                $query->where(function ($query) use ($filter) {
                    $query->where('id', 'LIKE', "%{$filter}%")
                        ->orWhere('filename', 'LIKE', "%{$filter}%");
                });
            })
            ->when($cancelled, function ($query) {
                $query->whereHas('extraState', function (Builder $query) {
                    $query->where('extra_state', '1');
                });
            })
            ->when($replaced, function ($query) {
                $query->whereHas('extraState', function (Builder $query) {
                    $query->where('extra_state', '2');
                });
            })
            ->with(['documentGroup', 'extraState'])
            ->latest()
            ->paginate(15)
            ->appends([
                'filter' => $filter,
                'cancelled' => $cancelled ? "true" : "false",
               'replaced' => $replaced ? "true" : "false",
            ]);

        return Inertia::render('Document/Index', [
            'documents' => $documents,
            'filters' => [
                'filter' => $filter,
                'cancelled' => $cancelled,
                'replaced' => $replaced,
            ]
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


    public function adminShow(Document $document)
    {
        $document->load(['documentGroup', 'extraState']);
        return Inertia::render('Document/Show')
            ->with(compact('document'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        if ($document->extraState?->extra_state === Document::ExtraStateCancelled) {
            return inertia('Document/Cancelled')
                ->with(compact('document'));
        }

        if ($document->extraState?->extra_state === Document::ExtraStateReplaced) {
            return inertia('Document/Replaced')
                ->with(compact('document'))
                ->with('replacement', $document->extraState->extra_state_text);
        }

        // Έλεγξε μήπως δεν έχει πάρει ψηφιακή υπογραφή ή το group δεν είναι δημοσιευμένο
        if (
            !file_exists(storage_path("app"). "/{$document->document_group_id}/signed/{$document->id}.pdf") ||
            !$document->documentGroup->published
            ) {
            $ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
                "{$_SERVER['HTTP_X_FORWARDED_FOR']} -> {$_SERVER['REMOTE_ADDR']}" :
                "{$_SERVER['REMOTE_ADDR']}";
            Log::warning("Το έγγραφο με id '{document_id}' δεν έχει αποθηκευμένο αρχείο ή η ομάδα δεν είναι δημοσιευμένη. [{ip_address}]", [
                'document_id' => $document->id,
                'ip_address' => $ip_address
            ]);
            return inertia('Error/DocumentNotFound');
        }

        return Inertia::location(route('document.download', $document));
    }

    public function download(Document $document)
    {
        // Κανονικά δεν θα έπρεπε να κάνουμε εκ νέου τους ελέγχους
        // αλλά προσπαθούμε να αποφύγουμε την περίπτωση που για κάποιο λόγο
        // κάποιος χρησιμοποιήσει το /download απευθείας
        if ($document->extraState?->extra_state === Document::ExtraStateCancelled) {
            return inertia('Document/Cancelled')
                ->with(compact('document'));
        }

        if ($document->extraState?->extra_state === Document::ExtraStateReplaced) {
            return inertia('Document/Replaced')
                ->with(compact('document'))
                ->with('replacement', $document->extraState->extra_state_text);
        }

        // Έλεγξε μήπως δεν έχει πάρει ψηφιακή υπογραφή ή το group δεν είναι δημοσιευμένο
        if (
            !file_exists(storage_path("app"). "/{$document->document_group_id}/signed/{$document->id}.pdf") ||
            !$document->documentGroup->published
            ) {
            $ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
                "{$_SERVER['HTTP_X_FORWARDED_FOR']} -> {$_SERVER['REMOTE_ADDR']}" :
                "{$_SERVER['REMOTE_ADDR']}";
            Log::warning("Το έγγραφο με id '{document_id}' δεν έχει αποθηκευμένο αρχείο ή η ομάδα δεν είναι δημοσιευμένη. [{ip_address}]", [
                'document_id' => $document->id,
                'ip_address' => $ip_address
            ]);
            return inertia('Error/DocumentNotFound');
        }

        return response()->download(
            storage_path("app"). "/{$document->document_group_id}/signed/{$document->id}.pdf",
            "{$document->filename}",
            [
                'Cache-Control' => 'no-cache, must-revalidate'
            ]
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

    public function cancel(Document $document, HttpRequest $request)
    {
        $validated = $request->validate([
            'cancelText' => ['string', 'nullable']
        ]);

        $cancelText = $validated['cancelText']?? "";

        if ($document->extraState) {
            $document->extraState->extra_state = Document::ExtraStateCancelled;
            $document->extraState->extra_state_text = $cancelText;
            $document->extraState->save();
        } else {
            $extraState = new DocumentExtraState([
                'extra_state' => Document::ExtraStateCancelled,
                'extra_state_text' => $cancelText,
            ]);
            $document->extraState()->save($extraState);
        }

        return response()->json(['result' => 'OK']);
    }

    public function restoreState(Document $document)
    {
        if ($document->extraState) {
            $document->extraState->delete();
        }

        return response()->json(['result' => 'OK']);
    }

    public function replace(Document $document, HttpRequest $request)
    {
        $validated = $request->validate([
            'replacement' => ['required','string']
        ]);

        $new_id = $validated['replacement'];

        $new_document = Document::find($new_id);

        if ($new_document) {
            if ($document->extraState) {
                $document->extraState->extra_state = Document::ExtraStateReplaced;
                $document->extraState->extra_state_text = $new_id;
                $document->extraState->save();
            } else {
                $extraState = new DocumentExtraState([
                    'extra_state' => Document::ExtraStateReplaced,
                    'extra_state_text' => $new_id,
                ]);
                $document->extraState()->save($extraState);
            }
        } else {
            return response()->json([
                'result' => "Not found",
                'error' => "Δεν υπάρχει έγγραφο με id '{$new_id}'"
            ]);
        }

        return response()->json(['result' => 'OK']);
    }
}

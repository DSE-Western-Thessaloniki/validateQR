<?php

namespace App\Http\Requests;

use App\Models\Document;
use App\Services\FileService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReplaceWithFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['file', 'mimes:pdf', 'max:5120', 'required']
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(FileService $fileService): array
    {
        return [
            function (Validator $validator) use ($fileService) {
                if ($this->filenameAlreadyExists($fileService)) {
                    $validator->errors()->add(
                        'file',
                        'Ένα αρχείο με το ίδιο όνομα υπάρχει ήδη στην ομάδα!'
                    );
                }
            }
        ];
    }

    public function filenameAlreadyExists(FileService $fileService): bool
    {
        $document = request()->route('document');

        /** @var UploadedFile */
        $file = request()->file('file');
        $filename = $fileService->sanitizeFilename($file->getClientOriginalName());

        // Έλεγξε αν υπάρχει ήδη έγγραφο με το ίδιο όνομα
        $existingDocument = Document::where('document_group_id', $document->documentGroup->id)
                ->where('filename', $filename)
                ->first();

        if ($existingDocument) {
            return true;
        }

        return false;
    }
}

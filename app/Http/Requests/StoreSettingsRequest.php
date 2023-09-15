<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingsRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'image' => ['file', 'mimes:png,jpg', 'nullable'],
            'qr_side' => ['required','numeric','min:0','max:2'],
            'qr_top_margin' => ['required','numeric','min:0'],
            'qr_side_margin' => ['required','numeric','min:0'],
            'qr_scale' => ['required','numeric','min:0'],
            'img_side' => ['required','numeric','min:0','max:2'],
            'img_top_margin' => ['required','numeric','min:0'],
            'img_side_margin' => ['required','numeric','min:0'],
            'img_scale' => ['required','numeric','min:0'],
        ];
    }
}

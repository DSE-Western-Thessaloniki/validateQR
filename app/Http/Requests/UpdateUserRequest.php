<?php

namespace App\Http\Requests;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role === User::Administrator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $passwordRule = array_filter(
            array_merge($this->passwordRules(), ['nullable']),
            fn($item) => $item !== 'required',
        );

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $passwordRule,
            'role' => ['required', Rule::in(User::Author, User::Administrator)]
        ];
    }
}

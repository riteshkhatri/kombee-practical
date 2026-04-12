<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|alpha_num|max:255',
            'last_name' => 'required|string|alpha_num|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'contact_number' => 'required|string|regex:/^[\d\s+\-\()]+$/|max:20',
            'postcode' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'hobbies' => 'nullable|array',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'password' => 'required|string|min:8|confirmed',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ];
    }
}

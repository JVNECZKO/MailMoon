<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
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
        $contactList = $this->route('contact_list');
        $contactId = $this->route('contact')?->id;

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('contacts')
                    ->ignore($contactId)
                    ->where(fn ($query) => $contactList ? $query->where('contact_list_id', $contactList->id) : $query),
            ],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}

<?php

namespace App\Http\Requests\Mails;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmailSendRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'success' => false
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            '*.message_body' => 'required|string',
            '*.message_subject' => 'required|string:',
            '*.to_email_address' => 'required|string|email|max:254',
        ];
    }

    public function validationData(): array|string|null
    {
        return $this->post();
    }
}

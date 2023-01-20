<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'to' => ['required', 'string|array'],
            'subject' => ['required', 'string'],
            'messsage' => ['required']

        ];
    }

    public function messages(): array
    {
        return [
            'to.required' => 'The email you wish to send an email to is required',
            'subject.required' => 'The subject of the email is required',
            'message.required' => 'The content of the email is required'
        ];
    }
}

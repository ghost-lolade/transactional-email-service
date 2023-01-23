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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'to' => ['required', 'email'],
            'subject' => ['required', 'string'],
            'message.text' => ['required_if:text_format,true'],
            'message.html' => ['required_if:html_format,true'],
            'message.markdown' => ['required_if:xml_format,true'],
        ];
    }

    public function messages(): array
    {
        return [
            'to.required' => 'The email you wish to send an email to is required',
            'subject.required' => 'The subject of the email is required',
            'message.required' => 'The content of the email is required',
            'format.required' => 'The format in which you want to send the email is required'
        ];
    }
}

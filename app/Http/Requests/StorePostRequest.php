<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePostRequest extends FormRequest
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
     * @return array <string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'nullable|string',
            'attachments' => 'array|max:50',
            'attachments.*' => [
                'file',
                File::types([
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'webp',
                    'mp3',
                    'wav',
                    'mp4',
                    "doc",
                    "docx",
                    "pdf",
                    "csv",
                    "xls",
                    "xlsx",
                    "zip"
                ])->max(500 * 1024 * 1024)
            ],
            'user_id' => 'integer|exists:users,id',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'body' => $this->input('body') ?: '',
            'user_id' => $this->user()->id,
        ]);
    }
}

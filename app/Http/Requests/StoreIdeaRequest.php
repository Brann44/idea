<?php

namespace App\Http\Requests;

use App\IdeaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIdeaRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'image' => ['nullable', 'image', 'max:2048'], // máx 2MB
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'url'],
            'steps' => ['nullable', 'array'],
            'steps.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}

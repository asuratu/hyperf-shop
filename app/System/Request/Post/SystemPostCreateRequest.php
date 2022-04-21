<?php

namespace App\System\Request\Post;

use Hyperf\Validation\Request\FormRequest;

class SystemPostCreateRequest extends FormRequest
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
            'name' => 'required|max:30',
            'code' => 'required|min:3|max:100',
        ];
    }
}
<?php

namespace App\Shop\Request\Users;

use Hyperf\Validation\Request\FormRequest;

/**
 * 用户管理验证数据类 (Update)
 */
class ShopUsersUpdateRequest extends FormRequest
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
            
        ];
    }
}
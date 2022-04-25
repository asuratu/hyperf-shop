<?php

namespace Api\Request\Users;

use Hyperf\Validation\Request\FormRequest;

/**
 * 用户账号密码登录验证数据类.
 */
class ShopUserLoginRequest extends FormRequest
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
            'username' => 'required|max:20',
            'password' => 'required|min:6',
        ];
    }
}

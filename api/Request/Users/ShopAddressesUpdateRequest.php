<?php

namespace Api\Request\Users;

use Hyperf\Validation\Request\FormRequest;

/**
 * 收货地址管理验证数据类 (Update)
 */
class ShopAddressesUpdateRequest extends FormRequest
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

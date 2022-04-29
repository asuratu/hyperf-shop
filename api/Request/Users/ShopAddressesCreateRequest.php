<?php

namespace Api\Request\Users;

use Hyperf\Validation\Request\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * 收货地址管理验证数据类 (Create)
 */
class ShopAddressesCreateRequest extends FormRequest
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
    #[ArrayShape(['province' => "string", 'city' => "string", 'district' => "string", 'address' => "string", 'zip' => "string", 'contact_name' => "string", 'contact_phone' => "string"])]
    public function rules(): array
    {
        return [
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'address' => 'required',
            'zip' => 'required',
            'contact_name' => 'required',
            'contact_phone' => 'required',
        ];
    }
}

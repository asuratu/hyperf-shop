<?php

declare(strict_types=1);

namespace Api\Request\Product;

use App\Shop\Model\ShopProductSku;
use Hyperf\Validation\Request\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class AddCartRequest extends FormRequest
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
    #[ArrayShape(['amount' => "string[]", 'sku_id' => "string[]"])]
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ShopProductSku::find($value)) {
                        return $fail('该商品不存在');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('该商品未上架');
                    }
                    if ($sku->stock === 0) {
                        return $fail('该商品已售完');
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        return $fail('该商品库存不足');
                    }
                },
            ],
        ];
    }

    #[ArrayShape(['amount' => "string"])]
    public function attributes(): array
    {
        return [
            'amount' => '商品数量'
        ];
    }

    #[ArrayShape(['sku_id.required' => "string", 'sku_id.exists' => "string"])]
    public function messages(): array
    {
        return [
            'sku_id.required' => '请选择商品',
        ];
    }
}

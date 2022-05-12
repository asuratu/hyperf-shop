<?php

declare(strict_types=1);

namespace Api\Request\Product;

use Hyperf\Validation\Request\FormRequest;

class SendReviewRequest extends FormRequest
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
            'reviews' => ['required', 'array'],
            'reviews.*.id' => [
                'required',
//                Rule::exists('shop_order_items', 'id')->where('order_id', $this->route('id'))
            ],
            'reviews.*.rating' => ['required', 'integer', 'between:1,5'],
            'reviews.*.review' => ['required'],
        ];


    }

    public function attributes(): array
    {
        return [
            'reviews.*.id' => '子订单号',
            'reviews.*.rating' => '评分',
            'reviews.*.review' => '评价',
        ];
    }
}

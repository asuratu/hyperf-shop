<?php

namespace Mine\Traits;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Mine\Constants\StatusCode;
use Mine\Exception\NormalStatusException;

/**
 * 验证器基类
 * Trait ValidationTrait
 * @package App\Foundation\Traits
 */
trait ValidationTrait
{
    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    /**
     * 验证异常
     * @param $data
     * @param $rules
     * @param $message
     */
    public function verifyParams($data, $rules, $message): void
    {
        $validator = $this->validationFactory->make($data, $rules, $message);
        if ($validator->fails()) {
            throw new NormalStatusException($validator->errors()->first(), StatusCode::ERR_VALIDATION);
        }
    }
}

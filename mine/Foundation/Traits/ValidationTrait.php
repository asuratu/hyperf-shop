<?php

namespace Mine\Foundation\Traits;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;
use Psr\Container\ContainerInterface;

/**
 * 验证器基类
 * Trait ValidationTrait
 * @package Mine\Foundation\Traits
 */
trait ValidationTrait
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
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
            throw new BusinessException(StatusCode::VALIDATE_FAILED, $validator->errors()->first());
        }
    }
}

<?php

namespace Mine\Traits;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Mine\Constants\StatusCode;
use Mine\Exception\NormalStatusException;
use Psr\Container\ContainerInterface;

/**
 * 验证器基类
 * Trait ValidationTrait
 * @package App\Foundation\Traits
 */
trait ValidationTrait
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * 验证异常
     * @param $data
     * @param $rules
     * @param $message
     */
    public function verifyParams($data, $rules, $message)
    {
        $validator = $this->validationFactory->make($data, $rules, $message);
        if ($validator->fails()) {
            throw new NormalStatusException(StatusCode::ERR_VALIDATION);
        }
    }
}

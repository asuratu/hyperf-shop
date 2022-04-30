<?php

declare(strict_types=1);

namespace Mine\Exception\Handler;

use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Hyperf\Validation\ValidationException;
use Mine\Constants\StatusCode;
use Mine\Exception\BusinessException;
use Mine\Exception\NoPermissionException;
use Mine\Exception\NormalStatusException;
use Mine\Exception\TokenException;
use Mine\Helper\MineCode;
use Mine\Traits\ControllerTrait;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 应用异常处理中心
 */
class AppExceptionHandler extends ExceptionHandler
{
    use ControllerTrait;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $format = [
            'success' => false,
            'message' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
        ];

        // 阻止异常冒泡
        $this->stopPropagation();

        switch ($throwable) {
            case $throwable instanceof TokenException:
                $format['code'] = MineCode::TOKEN_EXPIRED;
                $status = 401;
                break;
            case $throwable instanceof NoPermissionException:
                $format['code'] = MineCode::NO_PERMISSION;
                $status = 403;
                break;
            case $throwable instanceof ModelNotFoundException:
                $format['code'] = StatusCode::ERR_MAINTAIN;
                $status = 404;
                break;
            case $throwable instanceof ValidationException:
                $format['message'] = $throwable->validator->errors()->first();
                $format['code'] = MineCode::VALIDATE_FAILED;
                $status = 200;
                break;
            case $throwable instanceof BusinessException:
            case $throwable instanceof NormalStatusException:
//                logger('Exception log')->debug($throwable->getMessage());
                $status = 200;
                break;
            default:
                $format['message'] = '服务器错误 ' . $throwable->getMessage() . ':: FILE:' . $throwable->getFile() . ':: LINE: ' . $throwable->getLine();
                $status = 500;
        }

        return $response->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus($status)->withBody(new SwooleStream(Json::encode($format)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}

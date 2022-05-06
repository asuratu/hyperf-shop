<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);

namespace Mine;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MineResponse
 * @package MineServer
 */
class MineResponse extends Response
{
    /**
     * @param string|null $message
     * @param array|object $data
     * @param int $code
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function success(string $message = null, array|object $data = [], int $code = 200): ResponseInterface
    {
        $format = [
            'success' => true,
            'message' => $message ?: t('mineadmin.response_success'),
            'code' => $code,
            'data' => &$data,
        ];
        $format = $this->toJson($format);
        return $this->getResponse()
            ->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return parent::getResponse(); // TODO: Change the autogenerated stub
    }

    /**
     * @param string $message
     * @param int $code
     * @param array $data
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function error(string $message = '', int $code = 500, array $data = []): ResponseInterface
    {
        $format = [
            'success' => false,
            'code' => $code,
            'message' => $message ?: t('mineadmin.response_error'),
        ];

        if (!empty($data)) {
            $format['data'] = &$data;
        }

        $format = $this->toJson($format);
        return $this->getResponse()
            ->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }

    /**
     * 向浏览器输出图片
     * @param string $image
     * @param string $type
     * @return ResponseInterface
     */
    public function responseImage(string $image, string $type = 'image/png'): ResponseInterface
    {
        return $this->getResponse()->withHeader('Server', 'MineAdmin')
            ->withAddedHeader('content-type', $type)
            ->withBody(new SwooleStream($image));
    }
}


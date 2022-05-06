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

namespace Mine\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\MineModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Class SaveAspect
 * @package Mine\Aspect
 */
#[Aspect]
class SaveAspect extends AbstractAspect
{
    public $classes = [
        'Mine\MineModel::save',
        'Mine\ApiModel::save',
    ];

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        $instance = $proceedingJoinPoint->getInstance();

        if (config('mineadmin.data_scope_enabled') && $instance instanceof MineModel) {
            try {
                $user = user();
                // 设置创建人
                if (in_array('created_by', $instance->getFillable()) &&
                    is_null($instance->created_by)
                ) {
                    $user->check();
                    $instance->created_by = $user->getId();
                }

                // 设置更新人
                if (in_array('updated_by', $instance->getFillable())) {
                    $user->check();
                    $instance->updated_by = $user->getId();
                }
            } catch (Throwable $e) {
            }
        }

        // 生成ID
        if (!$instance->incrementing &&
            $instance->getPrimaryKeyType() === 'int' &&
            empty($instance->{$instance->getKeyName()})
        ) {
            $instance->setPrimaryKeyValue(snowflake_id());
        }
        return $proceedingJoinPoint->process();
    }
}

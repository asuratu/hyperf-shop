<?php

declare(strict_types=1);

namespace Mine\Constants;

use Hyperf\Constants\AbstractConstants;

/**
 * Class UploadCode
 * 上传相关错误码
 * 自定义业务代码规范如下：
 * 上传相关，4001……
 */
class UploadCode extends AbstractConstants
{
    /**
     * @Message("上传文件类型不正确")
     */
    const ERR_UPLOAD_TYPE = 4001;

    /**
     * @Message("上传文件尺寸不正确")
     */
    const ERR_UPLOAD_SIZE = 4002;

}


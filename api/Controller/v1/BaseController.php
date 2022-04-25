<?php

namespace Api\Controller\v1;

use Mine\MineApi;
use Mine\Traits\ValidationTrait;

class BaseController extends MineApi
{
    use ValidationTrait;
}

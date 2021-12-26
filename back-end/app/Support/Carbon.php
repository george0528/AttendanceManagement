<?php

namespace App\Support;

use Carbon\Carbon as BaseCarbon;

class Carbon extends BaseCarbon
{
  /**
   * Default format to use for __toString method when type juggling occurs.
   *
   * @var string
   */
  public const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i';
}
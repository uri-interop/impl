<?php
declare(strict_types=1);

namespace UriInterop\Impl;

use Exception;
use UriInterop\Interface\UriThrowable;

class UriException extends Exception implements UriThrowable
{
}

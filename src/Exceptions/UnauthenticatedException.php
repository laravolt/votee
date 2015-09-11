<?php
namespace Laravolt\Votee\Exceptions;

class UnauthenticatedException extends \Exception
{
    protected $message = 'unauthenticated user';
    protected $code = 401;
}

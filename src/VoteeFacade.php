<?php namespace Laravolt\Votee;

use Illuminate\Support\Facades\Facade;

class VoteeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'votee';
    }
}

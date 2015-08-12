<?php

namespace Laravolt\Votee\Models;

use Illuminate\Database\Eloquent\Model;

class VoteCounter extends Model
{

    protected $table = 'voteable_counter';

    public $timestamps = false;

    public function voteable()
    {
        return $this->morphTo();
    }

}

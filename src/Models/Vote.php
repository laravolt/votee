<?php

namespace Laravolt\Votee\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model {

    protected $table = 'voteable';

    protected $fillable = ['user_id'];

    public function voteable()
    {
        return $this->morphTo();
    }

    public function getIsUpAttribute()
    {
        return $this->attributes['value'] == config('votee.values.up');
    }

    public function getIsDownAttribute()
    {
        return $this->attributes['value'] == config('votee.values.down');
    }

}

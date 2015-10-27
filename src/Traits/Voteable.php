<?php

namespace Laravolt\Votee\Traits;

use Laravolt\Votee\Models\Vote;
use Laravolt\Votee\Models\VoteCounter;

trait Voteable
{

    /**
     * Collection of the votes on this record
     */
    public function votes()
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function upVotes()
    {
        return $this->votes()->whereValue(config('votee.values.up'));
    }

    public function downVotes()
    {
        return $this->votes()->whereValue(config('votee.values.down'));
    }

    /**
     * Counter is a record that stores the total vote up/down for the
     * morphed record
     */
    public function voteCounter()
    {
        return $this->morphOne(VoteCounter::class, 'voteable');
    }

    public function scopeMostVoted($query)
    {
        // not working
        //return $query->with(['voteCounter' => function($q){
        //    return $q->orderBy('up', 'DESC');
        //}]);

        $relatedClass = new static;
        $class = $relatedClass->getMorphClass();
        $table = $relatedClass->getTable();
        $primary = $relatedClass->getKeyName();

        $query->selectRaw("$table.*, voteable_id, voteable_type, IFNULL(up, 0), IFNULL(down, 0), IFNULL(up, 0) - IFNULL(down, 0) as total")
            ->leftJoin('voteable_counter', 'voteable_counter.voteable_id', '=', "$table.$primary")
              ->where(function ($queryWhere) use ($class) {
                  return $queryWhere->where('voteable_type', '=', $class)->orWhere('voteable_type', '=', null);
              })
              ->orderBy('total', 'DESC')->orderBy('up', 'DESC');
    }

    public function getVoteUpAttribute()
    {
        return $this->voteCounter ? $this->voteCounter->up : 0;
    }

    public function getVoteDownAttribute()
    {
        return $this->voteCounter ? $this->voteCounter->down : 0;
    }
}

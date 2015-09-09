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

        $query->leftJoin('voteable_counter', 'voteable_counter.voteable_id', '=', 'content.id')
        ->where('voteable_type', '=', (new static)->getMorphClass())
        ->orderBy('up', 'DESC');
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

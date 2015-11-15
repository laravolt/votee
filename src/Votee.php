<?php

namespace Laravolt\Votee;

use Laravolt\Votee\Exceptions\UnauthenticatedException;
use Laravolt\Votee\Models\Vote;
use Laravolt\Votee\Models\VoteCounter;

class Votee
{
    const NEUTRAL_TO_UP = 1;
    const UP_TO_NEUTRAL = 2;
    const DOWN_TO_UP = 3;
    const NEUTRAL_TO_DOWN = 4;
    const DOWN_TO_NEUTRAL = 5;
    const UP_TO_DOWN = 6;

    protected $app;
    protected $user;
    protected $config;

    /**
     * Create a new Skeleton Instance
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->user = $this->app->auth->user();
        $this->config = config('votee');
    }

    public function render($content, $options = [])
    {
        $vote = $this->getExistingVote($content, $this->user);

        return view("votee::{$this->config['presenter']}.buttons", compact('vote', 'content', 'options'))->render();
    }

    public function js()
    {

    }

    public function voteUp($content, $user = null)
    {
        return $this->vote(config('votee.values.up'), $content, $user);
    }

    public function voteDown($content, $user = null)
    {
        return $this->vote(config('votee.values.down'), $content, $user);
    }

    protected function vote($value, $content, $user = null)
    {
        // user current auth user if $user not present
        if (!$user) {
            $user = $this->user;
        }

        // sorry, we cannot process vote request if $user not present
        if (!$user) {
            throw new UnauthenticatedException;
        }

        $vote = $this->getExistingVote($content, $user);
        $direction = $this->getDirection($value, $vote);

        switch ($direction) {
            case self::NEUTRAL_TO_UP:
                $overridenValue = config('votee.values.up');
                break;
            case self::UP_TO_NEUTRAL:
                $overridenValue = config('votee.values.neutral');
                break;
            case self::DOWN_TO_UP:
                $overridenValue = config('votee.values.up');
                break;
            case self::NEUTRAL_TO_DOWN:
                $overridenValue = config('votee.values.down');
                break;
            case self::DOWN_TO_NEUTRAL:
                $overridenValue = config('votee.values.neutral');
                break;
            case self::UP_TO_DOWN:
                $overridenValue = config('votee.values.down');
                break;
        }

        if (!$vote) {
            $vote = new Vote();
            $vote->user_id = $user->id;
            $vote->value = $overridenValue;
            $content->votes()->save($vote);
        } else {
            $vote->value = $overridenValue;
            $vote->save();
        }

        $counter = $this->reloadCounter($content, $direction);

        return [
            'value'     => $vote['value'],
            'vote_up'   => $counter['up'],
            'vote_down' => $counter['down'],
        ];
    }

    protected function getExistingVote($content, $user)
    {
        if (!$this->user) {
            return false;
        }
        return $content->votes()->where('user_id', '=', $user->getAuthIdentifier())->first();
    }

    protected function reloadCounter($content, $direction)
    {
        $counter = $content->voteCounter()->first();

        $event = false;
        switch ($direction) {
            case self::NEUTRAL_TO_UP:
                $event = 'like';
                break;
            case self::UP_TO_NEUTRAL:
                $event = 'unlike';
                break;
            case self::DOWN_TO_UP:
                $event = 'like';
                break;
            case self::NEUTRAL_TO_DOWN:
                $event = 'dislike';
                break;
            case self::DOWN_TO_NEUTRAL:
                $event = 'undislike';
                break;
            case self::UP_TO_DOWN:
                $event = 'dislike';
                break;
        }

        if (!$counter) {
            $counter = new VoteCounter();
        }
        $counter->up = $content->upVotes()->count();
        $counter->down = $content->downVotes()->count();

        $saved = $content->voteCounter()->save($counter);
        if ($saved && $event) {
            event('votee.' . $event, [$this->app->auth->user(), $content, $counter]);
        }

        return $saved;
    }

    protected function getDirection($value, $vote)
    {
        if (!$vote) {
            $voteValue = config('votee.values.neutral');
        } else {
            $voteValue = $vote['value'];
        }

        switch ($voteValue) {
            case config('votee.values.neutral'):
                switch ($value) {
                    case config('votee.values.up'):
                        return self::NEUTRAL_TO_UP;
                        break;
                    case config('votee.values.down'):
                        return self::NEUTRAL_TO_DOWN;
                        break;
                }
                break;
            case config('votee.values.up'):
                switch ($value) {
                    case config('votee.values.up'):
                        return self::UP_TO_NEUTRAL;
                        break;
                    case config('votee.values.down'):
                        return self::UP_TO_DOWN;
                        break;
                }
                break;
            case config('votee.values.down'):
                switch ($value) {
                    case config('votee.values.up'):
                        return self::DOWN_TO_UP;
                        break;
                    case config('votee.values.down'):
                        return self::DOWN_TO_NEUTRAL;
                        break;
                }
                break;
        }
    }
}

<?php

namespace Laravolt\Votee;

use Laravolt\Votee\Exceptions\UnauthenticatedException;
use Laravolt\Votee\Models\Vote;
use Laravolt\Votee\Models\VoteCounter;
use Illuminate\Database\Eloquent\Model;

class Votee
{
    const NEUTRAL_TO_UP = 1;
    const UP_TO_NEUTRAL = 2;
    const DOWN_TO_UP = 3;
    const NEUTRAL_TO_DOWN = 4;
    const DOWN_TO_NEUTRAL = 5;
    const UP_TO_DOWN = 6;

    protected $app;
    protected $userId;
    protected $config;

    /**
     * Create a new Skeleton Instance
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->userId = $app->auth->id();
        $this->config = config('votee');
    }

    public function render($content)
    {
        $content = $this->getContentObject($content);
        $vote = $this->getExistingVote($content, $this->userId);

        return view("votee::{$this->config['presenter']}.buttons", compact('vote', 'content'))->render();
    }

    public function js()
    {

    }

    public function voteUp($content, $type, $userId = null)
    {
        $content = $this->getContentObject($content, $type);

        return $this->vote(config('votee.values.up'), $content, $userId);
    }

    public function voteDown($content, $type, $userId = null)
    {
        $content = $this->getContentObject($content, $type);

        return $this->vote(config('votee.values.down'), $content, $userId);
    }

    protected function vote($value, $content, $userId = null)
    {
        // use ID from logged in user if userId not present
        if (!$userId) {
            $userId = $this->userId;
        }

        // sorry, we cannot process vote request if userId not present
        if (!$userId) {
            throw new UnauthenticatedException;
        }

        $vote = $this->getExistingVote($content, $userId);
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
            $vote->user_id = $userId;
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

    protected function getExistingVote($content, $userId)
    {
        return $content->votes()->where('user_id', '=', $userId)->first();
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
            event('votee.' . $event, [$this->app->auth->user(), $content]);
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

    protected function getContentObject($content, $type = null)
    {
        if ($content instanceof Model) {
            return $content;
        }

        if ($type) {
            $class = $type;
        }

        return with(new $class)->findOrFail($content);
    }

}

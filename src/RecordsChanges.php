<?php

namespace RMoore\ChangeRecorder;

trait RecordsChanges
{
    protected static function bootRecordsChanges()
    {
        foreach (static::getModelEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->makeChange($event);
            });
        }
    }

    public function makeChange($event)
    {
        $changed = $this->getDirty();
        $before = array_intersect_key($this->original, $changed);

        $user = auth()->id();
        if (! $user) {
            $user = 0;
        }

        Change::create([
            'subject_id'   => $this->id,
            'subject_type' => get_class($this),
            'event_name'   => $this->getEventName($this, $event, array_keys($changed)),
            'user_id'      => $user,
            'before'       => $before,
            'after'        => $changed,
        ]);
    }

    private function getShortClassName()
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    protected function getEventName($model, $action, $changes = [])
    {
        $name = $this->getShortClassName();
        $change = '';
        if ($action == 'updated') {
            $count = count($changes);
            if ($count == 1 || ($count == 2 && array_key_exists('updated_at', $changes))) {
                $change = "_{$changes[0]}";
            }
        }

        return "{$action}_{$name}{$change}";
    }

    protected static function getModelEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return [
            'created', 'updated', 'deleted', //'restored',
        ];
    }

    public function changes()
    {
        return $this->morphMany(Change::class, 'subject');
    }

    public function collaboratorIds()
    {
        return $this->changes->pluck('user_id');
    }

    public function collaborators()
    {
        $userClass = config('auth.providers.users.model');

        return $this->hasManyThrough($userClass, Change::class);
    }

    public function getHistory($field = null)
    {
        if (! $field) {
            return $this->changes;
        }
        $res = [];
        $class = $this->getShortClassName();
        foreach ($this->changes->where('event_name', "updated_{$class}_{$field}") as $change) {
            $res[] = [
                'timestamp' => $change->created_at->timestamp,
                'diff-date' => $change->created_at->diffForHumans(),
                'before'    => $change->before[$field],
                'after'     => $change->after[$field],
            ];
        }

        return $res;
    }

    public function __call($method, $parameters)
    {
        $m = $this->_changeMagic($method, $parameters);

        return ($m === null) ? parent::__call($method, $parameters) : $m;
    }

    protected function _changeMagic($method, $parameters)
    {
        //check for history
        $matches = [];
        if (preg_match('/(?:get)?(.+)(?=History)/', $method, $matches)) {
            $attr = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $matches[1]));

            return $this->getHistory($attr);
        }
    }
}

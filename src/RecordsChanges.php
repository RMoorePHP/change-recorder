<?php

namespace RMoore\ChangeRecorder;

trait RecordsChanges {

	protected static function bootRecordsChanges(){
        foreach(static::getModelEvents() as $event){
	        static::$event(function($model) use ($event) {
	        	$model->makeChange($event);
	        });
	    }
    }

    public function makeChange($event){
    	$changed = $this->getDirty();
    	$before = array_intersect_key($this->original, $changed);

        $user = \Auth::id();
        if(!$user)
            $user = 0;

        Change::create([
            'subject_id' => $this->id,
            'subject_type' => get_class($this),
            'event_name' => $this->getEventName($this, $event, array_keys($changed)),
            'user_id' => $user,
            'before' => json_encode($before),
            'after' => json_encode($changed),
        ]);
    }

    protected function getEventName($model, $action, $changes = []){
    	$name = strtolower((new \ReflectionClass($model))->getShortName());
        $change = '';
        if($action == 'updated'){
            if($changes->length == 1 || $changes->length == 2)
                $change = "_{$changes[0]}";
        }
    	return "{$action}_{$name}{$change}";
    }

    protected static function getModelEvents(){
    	if(isset(static::$recordEvents)){
    		return static::$recordEvents;
    	}
    	return [
            'created', 'updated', 'deleted', //'restored',
        ];
    }

    public function changes(){
    	return $this->morphMany(Change::class, 'subject');
    }


}
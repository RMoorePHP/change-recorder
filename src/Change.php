<?php

namespace RMoore\ChangeRecorder;

use Illuminate\Database\Eloquent\SoftDeletes;

class Change extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    protected $fillable = [
    	'subject_id',
        'subject_type',
        'event_name',
        'user_id',
        'before',
        'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function subject(){
    	return $this->morphTo();
    }
}

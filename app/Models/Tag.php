<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'color'
    ];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BxUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'bx_id',
        'name',
        'last_name',
        'email',
        'work_position',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

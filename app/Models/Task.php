<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'bx_id',
        'title',
        'description',
        'priority',
        'created_by',
        'created_date',
        'responsible_id',
        'closed_by',
        'closed_date',
        'deadline',
        'status',
        'sub_status',
    ];

    public function creator()
    {
        return $this->belongsTo(BxUser::class, 'created_by', 'id');
    }

    public function responsible()
    {
        return $this->belongsTo(BxUser::class, 'responsible_id', 'id');
    }

    public function closer()
    {
        return $this->belongsTo(BxUser::class, 'closed_by', 'id');
    }
}

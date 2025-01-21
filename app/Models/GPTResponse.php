<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GPTResponse extends Model
{
    use HasFactory;

    protected $fillable = ['input_data', 'response_data', 'created_at', 'updated_at'];
}

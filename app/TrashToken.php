<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrashToken extends Model
{
    protected $table = 'trash_tokens';
    public $timestamps = false;

    protected $fillable = ['token'];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Todos extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'id_name';
}

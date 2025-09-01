<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addusers extends Model
{
    protected $table = 'users';
    protected $fillable=['fname','lname','email','password','enabled','last_login','last_access_ip','serialized'];
    public $timestamps=false;
}

<?php

use Illuminate\Database\Eloquent\Model;

class Users extends Model {
    
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'status', 'password', 'gender', 'name', 'birth_date', 'email'
    ];
}
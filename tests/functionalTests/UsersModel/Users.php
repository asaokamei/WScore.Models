<?php

use Illuminate\Database\Eloquent\Model;

class Users extends Model {
    
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'status', 'gender', 'name', 'birth_date', 'email'
    ];
}
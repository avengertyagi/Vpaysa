<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    protected $table = "user_address";
    protected $fillable = ['user_id','street_one','street_two','city','state','country','post_code',
    ];
}

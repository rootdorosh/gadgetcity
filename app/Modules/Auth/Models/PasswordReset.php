<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    /*
     * @var string
     */
    public $table = 'auth_password_resets';

    /**
     * @var string
     */
    protected $primaryKey = 'email';

    /**
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
    ];
}

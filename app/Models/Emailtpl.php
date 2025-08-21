<?php

namespace App\Models;

use App\Services\CacheManager;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Emailtpl extends Model
{

    use SoftDeletes;

    protected $table = 'emailtpls';

    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($emailtpl) {
            CacheManager::forgetEmailTemplate($emailtpl->tpl_token);
        });
        
        static::deleted(function ($emailtpl) {
            CacheManager::forgetEmailTemplate($emailtpl->tpl_token);
        });
    }

}

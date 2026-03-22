<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'company_name',
        'site_title',
        'site_description',
        'logo',
        'email',
        'phone',
        'address',
        'facebook_url',
        'linkedin_url',
        'instagram_url',
        'twitter_url',
        'footer_text',
    ];
}

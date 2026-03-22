<?php

namespace App\Http\Resources;

use App\Support\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'site_title' => $this->site_title,
            'site_description' => $this->site_description,
            'logo' => PublicFile::url($this->logo),
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'facebook_url' => $this->facebook_url,
            'linkedin_url' => $this->linkedin_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,
            'footer_text' => $this->footer_text,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

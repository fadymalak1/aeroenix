<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Http\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use App\Support\PublicFile;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(Request $request): SiteSettingResource
    {
        $settings = $this->settings();

        $this->authorize('view', $settings);

        return new SiteSettingResource($settings);
    }

    public function update(UpdateSettingsRequest $request): SiteSettingResource
    {
        $settings = $this->settings();

        $this->authorize('update', $settings);

        $data = $request->validated();
        unset($data['logo_file']);

        if ($request->hasFile('logo_file')) {
            PublicFile::deleteIfStored($settings->logo);
            $data['logo'] = PublicFile::store($request->file('logo_file'), 'settings');
        }

        $settings->fill($data);
        $settings->save();

        return new SiteSettingResource($settings->fresh());
    }

    private function settings(): SiteSetting
    {
        $existing = SiteSetting::query()->first();
        if ($existing) {
            return $existing;
        }

        return SiteSetting::query()->create([
            'company_name' => null,
            'site_title' => null,
            'site_description' => null,
        ]);
    }
}

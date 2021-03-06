<?php

namespace Database\Seeders;

use App\Models\License;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class LicenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Fetch the github licenses
        $githubLicenseURL = config('app.github_license_url');
        $response = Http::get($githubLicenseURL, [
        ]);

        $licenseArray = json_decode($response, true);
        if ($response) {
            foreach ($licenseArray as $item) {
                $licenseUrl = $item['url'];
                if ($licenseUrl) {
                    $licenseResponse = Http::get($licenseUrl, [
                    ]);
                    if ($licenseResponse) {
                        $license = License::create([
                            'title' => $licenseResponse['name'],
                            'slug' => $licenseResponse['key'],
                            'spdx_id' => $licenseResponse['spdx_id'],
                            'url' => $licenseResponse['url'],
                            'node_id' => $licenseResponse['node_id'],
                            'html_url' => $licenseResponse['html_url'],
                            'description' => $licenseResponse['description'],
                            'implementation' => $licenseResponse['implementation'],
                            'permissions' => json_encode($licenseResponse['permissions']),
                            'body' => $licenseResponse['body'],
                        ]);
                    }
                }
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LicenseController extends Controller
{
    /**
     * @var list<string>
     */
    private const LICENSE_SUMMARY_FIELDS = ['id', 'title', 'description', 'category'];

    /**
     * Return All Licenses
     */
    public function index(Request $request): JsonResponse
    {
        $licenses = Cache::rememberForever('licenses', function (): array {
            return $this->licenseSummaries(
                License::query()
                    ->select(self::LICENSE_SUMMARY_FIELDS)
                    ->orderBy('category')
                    ->orderBy('title')
                    ->get()
            );
        });

        return response()->json($licenses);
    }

    /**
     * Return License for particular ID.
     */
    public function getLicensebyId(Request $request, int $id): JsonResponse
    {
        $license = $this->licenseSummaries(
            License::query()
                ->select(self::LICENSE_SUMMARY_FIELDS)
                ->where('id', $id)
                ->get()
        );

        return response()->json($license);
    }

    /**
     * @param  Collection<int, License>  $licenses
     * @return list<array<string, mixed>>
     */
    private function licenseSummaries(Collection $licenses): array
    {
        return $licenses
            ->map(fn (License $license): array => $license->only(self::LICENSE_SUMMARY_FIELDS))
            ->values()
            ->all();
    }
}

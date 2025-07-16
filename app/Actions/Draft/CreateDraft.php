<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\User;
use Illuminate\Support\Str;

class CreateDraft
{
    /**
     * Create a new draft for the user.
     */
    public function execute(User $user): Draft
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        $id = Str::uuid();
        $environment = env('APP_ENV', 'local');

        $path = $this->generateDraftPath($environment, $user_id, $id);
        $name = $this->generateDraftName($id);

        return Draft::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => '',
            'relative_url' => $this->generateRelativeUrl($id),
            'path' => $path,
            'owner_id' => $user_id,
            'team_id' => $team_id ?: null,
            'key' => $id,
        ]);
    }

    /**
     * Generate draft file path.
     */
    private function generateDraftPath(string $environment, int $user_id, string $id): string
    {
        return preg_replace(
            '~//+~',
            '/',
            $environment.'/'.$user_id.'/drafts/'.$id
        );
    }

    /**
     * Generate draft name from UUID.
     */
    private function generateDraftName(string $id): string
    {
        return 'Untitled Project (Draft: '.explode('-', $id)[0].')';
    }

    /**
     * Generate relative URL for draft.
     */
    private function generateRelativeUrl(string $id): string
    {
        return rtrim(
            preg_replace('~//+~', '/', '/'.$id),
            '/'
        );
    }
}

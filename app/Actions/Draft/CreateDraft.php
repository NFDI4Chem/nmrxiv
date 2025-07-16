<?php

namespace App\Actions\Draft;

use App\Models\Draft;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CreateDraft
{
    /**
     * Create a new draft for the user.
     */
    public function execute(User $user, array $options = []): Draft
    {
        [$user_id, $team_id] = $user->getUserTeamData();

        $id = Str::uuid();
        $environment = env('APP_ENV', 'local');

        $path = $this->generateDraftPath($environment, $user_id, $id);
        $name = $this->generateDraftName($id, $options);

        $draftData = [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->generateDescription($options),
            'relative_url' => $this->generateRelativeUrl($id),
            'path' => $path,
            'owner_id' => $user_id,
            'team_id' => $team_id ?: null,
            'key' => $id,
        ];

        // Add ELN-specific fields if provided
        if (isset($options['eln'])) {
            $draftData['eln'] = $options['eln'];
        }
        if (isset($options['external_id'])) {
            $draftData['external_id'] = $options['external_id'];
        }
        if (isset($options['callback_url'])) {
            $draftData['callback_url'] = $options['callback_url'];
        }
        if (isset($options['zip_url'])) {
            $draftData['zip_url'] = $options['zip_url'];
        }
        if (isset($options['release_date'])) {
            $draftData['release_date'] = $options['release_date'];
        }

        return Draft::create($draftData);
    }

    /**
     * Find existing draft by external ID and user/team.
     */
    public function findByExternalId(string $externalId, int $userId, int $teamId): ?Draft
    {
        return Draft::where('external_id', $externalId)
            ->where('owner_id', $userId)
            ->where('team_id', $teamId)
            ->first();
    }

    /**
     * Update existing draft with new data.
     */
    public function update(Draft $draft, array $updateData): Draft
    {
        $draft->update($updateData);
        return $draft->fresh();
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
    private function generateDraftName(string $id, array $options = []): string
    {
        if (isset($options['eln'])) {
            return 'ELN Import ('.strtoupper($options['eln']).': '.explode('-', $id)[0].')';
        }
        
        return 'Untitled Project (Draft: '.explode('-', $id)[0].')';
    }

    /**
     * Generate description based on options.
     */
    private function generateDescription(array $options = []): string
    {
        if (isset($options['eln'])) {
            return 'Draft created from ELN system: '.$options['eln'];
        }
        
        return '';
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

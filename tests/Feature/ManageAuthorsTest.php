<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageAuthorsTest extends TestCase
{

    // use RefreshDatabase;

    /**
     * Test if a author can be updated
     *
     * @return void
     */
    public function test_author_can_be_updated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $project = Project::factory()->create([
            'owner_id' => $user->id
        ]);

        $author = Author::factory()->create();

        $project->authors()->sync([$author->id => ['contributor_type' => 'Researcher', 'sort_order' => 0] ]);

        $body = [
            'authors' => [[
                'title'         => $author->title,
                'given_name'    => $author->given_name,
                'family_name'   => $author->family_name,
                'orcid_id'      => $author->orcid_id,
                'email_id'      => $author->email_id,
                'affiliation'   => $author->affiliation . '_ updated',
            ]]
        ];
        
        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->post('authors/'.$project->id, $body);

        $response->assertStatus(200);
    }

    /**
     * Test if a author can be deleted
     *
     * @return void
     */
    public function test_author_can_be_deleted()
    {
    }

    /**
     * API test to europemc to fetch author
     *
     * @return void
     */
    public function test_author_can_be_fetched_from_europemc_api()
    {
        /*$response = Http::get('https://www.ebi.ac.uk/europepmc/webservices/rest/search', [
            'query' => 'DOI:10.1002/mrc.4737',
            'format' => "json",
            'pageSize' => 1,
            'resulttype'=> "core",
            'synonym' => "true",
        ]);

        if($response && $response['resultList']){
            $response->assertStatus(200);
        } else {
            $response->assertStatus(500);
        }*/
    }

    /**
     * Test if the role of an author can be updated
     *
     * @return void
     */
    public function test_role_of_an_author_can_be_updated()
    {
    }
}

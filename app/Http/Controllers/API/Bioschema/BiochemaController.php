<?php

namespace App\Http\Controllers\API\Bioschema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;
use App\Models\Bioschema\BioSchema;
use App\Models\Project;
use App\Models\Study;
use App\Models\Dataset;
use App\Models\Bioschema\Study as StudySchema;
use App\Models\User;
use App\Models\Author;



class BiochemaController extends Controller
{
    public function schema(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
    {
        $user = User::where('username', $username)->firstOrFail();
        if($user){
            $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();  
        }
        if($project){
            $creativeWork = Schema::creativeWork();
            $creativeWork['@id'] = 'https://bioschemas.org/profiles/Study/0.2-DRAFT';
            $confromsTo = [];
            $confromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWork;
            

            $projectSchema = Schema::Project(); 
            $projectSchema['@id']= $project->uuid;
            $projectSchema['dct:conformsTo'] = $confromsTo;
            $authors = [];
            foreach ($project->authors as &$author) {
                $authorSchema = Schema::Person(); 
                $authorSchema-> givenName($author->given_name);
                $authorSchema->familyName($author->family_name);
                $authorSchema->identifier($author->orcid_id);
                $authorSchema-> email($author->email_id);
                $authorSchema-> affiliation($author->affiliation);
                array_push($authors, $authorSchema);
            }
            $projectSchema['author']  = $authors;
            $projectSchema->datePublished($project->release_date);
            $projectSchema->description($project->description);
            $projectSchema->name($project->name);
            $projectSchema->dateCreated($project->created_at);
            $tags = [];
            foreach ($project->tags as &$tag) {
                $tag = $tag->name; 
                array_push($tags, $tag);
            }
            $projectSchema->keywords($tags);
            $projectSchema->url($project->public_url);
            $projectSchema->license($project->license->url);
            
            $studies = [];
            foreach ($project->studies as &$study) {
                $studySchema = BioSchema::Study(); 
                $studySchema->name($study->name);
                $studySchema->url($study->public_url);
                
                $datasets = [];
                foreach ($study->datasets as &$dataset) {
                    $datasetSchema = Schema::Dataset(); 
                    $datasetSchema->name($dataset->name);
                    $datasetSchema->url($dataset->public_url);
                    array_push($datasets, $datasetSchema);
                }
                $studySchema->hasPart($datasets);
                array_push($studies, $studySchema);   
            }
            $projectSchema->hasPart($studies);
            
            if($studyName){
                // send study back with project info added to it\
                $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
                if($study){
                    $studySchema = BioSchema::Study(); 
                    $studySchema['@id']= $study->uuid;
                    $studySchema['dct:conformsTo'] = $confromsTo;
                    $studySchema->name($study->name);
                    $studySchema->description($study->description);
                    $tags = [];
                    foreach ($study->tags as &$tag) {
                        $tag = $tag->name; 
                        array_push($tags, $tag);
                    }
                    $studySchema->keywords($tags);
                    $studySchema->url($study->public_url);
                    $studySchema->license($study->license->url);
                    $datasets = [];
                    foreach ($study->datasets as &$dataset) {
                        $datasetSchema = Schema::Dataset(); 
                        $datasetSchema->name($dataset->name);
                        $datasetSchema->url($dataset->public_url);
                        array_push($datasets, $datasetSchema);
                    }
                    $studySchema->hasPart($datasets);
                    $projectSchema->relatedStudy($studySchema);
                    
                    
                    if($datasetName){
                        $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
                        // send dataset with project and study details
                        if($dataset){
                            $dataCatalog = Schema::dataCatalog();
                            $dataCatalog->name(env('APP_NAME'));
                            $dataCatalog->url('https://nmrxiv.org');

                            $creativeWorkDataset = Schema::creativeWork();
                            $creativeWorkDataset['@id'] = 'https://bioschemas.org/profiles/Dataset/1.0-RELEASE';
                            $DatasetconfromsTo = [];
                            $DatasetconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkDataset;
                            $datasetSchema = Schema::Dataset(); 
                            $datasetSchema['@id']= $dataset->uuid;
                            $datasetSchema['dct:conformsTo'] = $DatasetconfromsTo;
                            $datasetSchema->description($dataset->description);
                            $datasetSchema->name($dataset->name);
                            $datasetSchema->url($dataset->public_url);
                            $datasetSchema->license($study->license->url);
                            $datasetSchema->includedInDataCatalog($dataCatalog);
                            $datasetSchema->measurementTechnique("https://terminology.nfdi4chem.de/ts/ontologies/chmo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHMO_0000591");
                            $datasetSchema->isPartOf([$projectSchema]);
                            return $datasetSchema;
                        }
                    }
                }
            }
            return $projectSchema;
        }
    }
}

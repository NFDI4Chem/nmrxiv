<?php

namespace App\Http\Controllers\API\Schemas\Bioschema;

use App\Http\Controllers\Controller;
use App\Models\Bioschema\BioSchema;
use App\Models\Dataset;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;

class BiochemaController extends Controller
{
    public function schema(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
    {
        $user = User::where('username', $username)->firstOrFail();
        if ($user) {
            $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();
        }
        if ($project) {
            $creativeWork = Schema::creativeWork();
            $creativeWork['@id'] = 'https://bioschemas.org/profiles/Study/0.2-DRAFT';
            $confromsTo = [];
            $confromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWork;

            $authors = [];
            foreach ($project->authors as &$author) {
                $authorSchema = Schema::Person();
                $authorSchema->givenName($author->given_name);
                $authorSchema->familyName($author->family_name);
                $authorSchema->identifier($author->orcid_id);
                $authorSchema->email($author->email_id);
                $authorSchema->affiliation($author->affiliation);
                array_push($authors, $authorSchema);
            }
            $tags = [];
            foreach ($project->tags as &$tag) {
                $tag = $tag->name;
                array_push($tags, $tag);
            }

            $studies = ['studies' => []];
            foreach ($project->studies as &$study) {
                $datasets = ['datasets' => []];
                foreach ($study->datasets as &$dataset) {
                    $datasetSchema = Schema::Dataset();
                    $datasetSchema->name($dataset->name);
                    $datasetSchema->url($dataset->public_url);
                    array_push($datasets['datasets'], $datasetSchema);
                }

                $sample = $study->sample;
                $sampleSchema = BioSchema::Sample();
                $sampleSchema->name($sample->name);
                $inchi = [];
                foreach ($sample->molecules as &$molecule) {
                    array_push($inchi, $molecule->STANDARD_INCHI);
                }
                $sampleSchema->additionalProperty(['standard-inchi' => $inchi]);

                $studySchema = BioSchema::Study();
                $studySchema->name($study->name);
                $studySchema->url($study->public_url);
                $studySchema->about($sampleSchema);
                $studySchema->hasPart($datasets);

                array_push($studies['studies'], $studySchema);
            }

            $projectSchema = BioSchema::Project();
            $projectSchema['@id'] = $project->uuid;
            $projectSchema['dct:conformsTo'] = $confromsTo;
            $projectSchema['author'] = $authors;
            $projectSchema->datePublished($project->updated_at);
            $projectSchema->description($project->description);
            $projectSchema->name($project->name);
            $projectSchema->projectDomain('Chemistry');
            $projectSchema->projectSubject('Small molecules');
            $projectSchema->dateCreated($project->created_at);
            $projectSchema->keywords($tags);
            $projectSchema->url($project->public_url);
            $projectSchema->license($project->license->url);
            $projectSchema->hasPart($studies);

            if ($studyName) {
                // send study back with project info added to it\
                $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
                if ($study) {
                    $tags = [];
                    foreach ($study->tags as &$tag) {
                        $tag = $tag->name;
                        array_push($tags, $tag);
                    }

                    $datasets = ['datasets' => []];
                    foreach ($study->datasets as &$dataset) {
                        $datasetSchema = Schema::Dataset();
                        $datasetSchema->name($dataset->name);
                        $datasetSchema->url($dataset->public_url);
                        array_push($datasets['datasets'], $datasetSchema);
                    }

                    $creativeWorkSample = Schema::creativeWork();
                    $creativeWorkSample['@id'] = 'https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10';
                    $SampleconfromsTo = [];
                    $SampleconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkSample;

                    $sample = $study->sample;

                    $creativeWorkMolecule = Schema::creativeWork();
                    $creativeWorkMolecule['@id'] = 'https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE';
                    $MoleculeconfromsTo = [];
                    $MoleculeconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkMolecule;

                    $molecules = [];
                    foreach ($sample->molecules as &$molecule) {
                        $moleculeSchema = Schema::MolecularEntity();
                        $moleculeSchema['@id'] = $molecule->INCHI_KEY;
                        $moleculeSchema['dct:conformsTo'] = $MoleculeconfromsTo;
                        $moleculeSchema->name($molecule->name);
                        $moleculeSchema->inChI($molecule->STANDARD_INCHI);
                        $moleculeSchema->molecularFormula($molecule->FORMULA);
                        $moleculeSchema->molecularWeight($molecule->MOLECULAR_WEIGHT);
                        $moleculeSchema->smiles([$molecule->SMILES, $molecule->SMILES_CHIRAL, $molecule->CANONICAL_SMILES]);
                        $moleculeSchema->alternateName($molecule->CAS_NUMBER);
                        $moleculeSchema->hasRepresentation($molecule->MOL);
                        array_push($molecules, $moleculeSchema);
                    }

                    $sampleSchema = BioSchema::Sample();
                    $sampleSchema['@id'] = $sample->uuid;
                    $sampleSchema['dct:conformsTo'] = $SampleconfromsTo;
                    $sampleSchema->description($sample->description);
                    $sampleSchema->name($sample->name);
                    $sampleSchema->additionalProperty(['molecules' => $molecules]);

                    $studySchema = BioSchema::Study();
                    $studySchema['@id'] = $study->uuid;
                    $studySchema['dct:conformsTo'] = $confromsTo;
                    $studySchema->datePublished($study->updated_at);
                    $studySchema->description($study->description);
                    $studySchema->name($study->name);
                    $studySchema->studyDomain('Chemistry');
                    $studySchema->studySubject('Small molecules');
                    $studySchema->dateCreated($study->created_at);
                    $studySchema->keywords($tags);
                    $studySchema->url($study->public_url);
                    $studySchema->about($sampleSchema);
                    $studySchema->license($study->license->url);
                    $studySchema->hasPart($datasets);

                    $projectSchema->relatedStudy($studySchema);

                    if ($datasetName) {
                        $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
                        // send dataset with project and study details
                        if ($dataset) {
                            $dataCatalog = Schema::dataCatalog();
                            $dataCatalog->name(env('APP_NAME'));
                            $dataCatalog->url('https://nmrxiv.org');

                            $creativeWorkDataset = Schema::creativeWork();
                            $creativeWorkDataset['@id'] = 'https://bioschemas.org/profiles/Dataset/1.0-RELEASE';

                            $DatasetconfromsTo = [];
                            $DatasetconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkDataset;
                            $datasetSchema = Schema::Dataset();
                            $datasetSchema['@id'] = $dataset->uuid;
                            $datasetSchema['dct:conformsTo'] = $DatasetconfromsTo;
                            $datasetSchema->description($dataset->description);
                            $datasetSchema->license($study->license->url);
                            $datasetSchema->name($dataset->name);
                            $datasetSchema->url($dataset->public_url);
                            $datasetSchema->datePublished($dataset->updated_at);
                            $datasetSchema->includedInDataCatalog($dataCatalog);
                            $datasetSchema->measurementTechnique('https://terminology.nfdi4chem.de/ts/ontologies/chmo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHMO_0000591');
                            $datasetSchema->dateCreated($dataset->created_at);
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

<?php

namespace App\Http\Controllers\API\Schemas\Bioschema;

use App\Http\Controllers\Controller;
use App\Models\Bioschema\BioSchema;
use App\Models\Dataset;
use App\Models\NMRium;
use App\Models\Project;
use App\Models\Study;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;

class BiochemaController extends Controller
{
    /**
     * Implement Bioschemas types on nmrXiv project, study, and dataset to enable exporting
     * their metadata with a json endpoint, including the samples and molecules.
     */

    /**
     * Create a variable $confromsTo for Bioschemas' study creative work to be used
     * for the project and study schemas.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @return object $confromsTo
     */
    public function conforms()
    {
        $creativeWork = Schema::creativeWork();
        $creativeWork['@id'] = 'https://bioschemas.org/profiles/Study/0.2-DRAFT';
        $confromsTo = [];
        $confromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWork;

        return $confromsTo;
    }

    /**
     * Implement Bioschemas' dataset with only few properties to be included in the project
     * schema or the study schema.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     *
     * @param  App\Models\Dataset  $dataset
     * @return object $datasetSchema
     */
    public function datasetLite($dataset)
    {
        $datasetSchema = Schema::Dataset();
        $datasetSchema->name($dataset->name);
        $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);

        return  $datasetSchema;
    }

    /**
     * Implement Bioschemas' study with only few properties, including the sample and molecules,
     * to be included in the project
     * schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function studyLite($study)
    {
        $sample = $study->sample;
        $sampleSchema = BioSchema::BioSample();
        $molecules = [];
        foreach ($sample->molecules as &$molecule) {
            $moleculeSchema = Schema::MolecularEntity();
            $moleculeSchema->inChI($molecule->STANDARD_INCHI);
            array_push($molecules, $moleculeSchema);
        }
        $sampleSchema->hasBioChemEntityPart($molecules);
        $sampleSchema->name($sample->name);

        $studySchema = BioSchema::Study();
        $studySchema->name($study->name);
        $studySchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $studySchema->about($sampleSchema);

        $datasets = [];
        foreach ($study->datasets as &$dataset) {
            $datasetSchema = $this->datasetLite($dataset);
            array_push($datasets, $datasetSchema);
        }

        $studySchema->hasPart($datasets);

        return $studySchema;
    }

    /**
     * Implement Bioschemas' dataset, along with the project and study it belongs to.
     *
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     *
     * @param  App\Models\Dataset  $dataset
     * @param  App\Models\Study  $study
     * @param  App\Models\Project  $project
     * @return object $datasetSchema
     */
    public function dataset($dataset, $study, $project)
    {
        $dataCatalog = Schema::dataCatalog();
        $dataCatalog->name(env('APP_NAME'));
        $dataCatalog->url('https://nmrxiv.org');

        $creativeWorkDataset = Schema::creativeWork();
        $creativeWorkDataset['@id'] = 'https://bioschemas.org/profiles/Dataset/1.0-RELEASE';

        $DatasetconfromsTo = [];
        $DatasetconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkDataset;

        $nmrium = NMRium::where([['dataset_id', $dataset->id]])->firstOrFail();
        $info = json_decode($nmrium->nmrium_info)->spectra[0]->info;

        $nucleus = $info->nucleus;
        $solvent = $info->solvent;
        $dimension = $info->dimension;
        $probeName = $info->probeName;
        $experiment = $info->experiment;
        $temperature = $info->temperature;
        $baseFrequency = $info->baseFrequency;
        $fieldStrength = $info->fieldStrength;
        $numberOfScans = $info->numberOfScans;
        $pulseSequence = $info->pulseSequence;
        $spectralWidth = $info->spectralWidth;
        $numberOfPoints = $info->numberOfPoints;
        $relaxationTime = $info->relaxationTime;
        $acquisitionTime = $info->acquisitionTime;

        $datasetSchema = Schema::Dataset();
        $datasetSchema['@id'] = $dataset->uuid;
        $datasetSchema['dct:conformsTo'] = $DatasetconfromsTo;
        $datasetSchema->description('[nucleus:'.$nucleus.', solvent:'.$solvent.', dimension:'.$dimension.', probeName:'.$probeName.', experiment:'.$experiment.', temperature:'.$temperature.', baseFrequency:'.$baseFrequency.', fieldStrength:'.$fieldStrength.', numberOfScans:'.$numberOfScans.', pulseSequence:'.$pulseSequence.', spectralWidth:'.$spectralWidth.', numberOfPoints:'.$numberOfPoints.', relaxationTime:'.$relaxationTime.', acquisitionTime:'.$acquisitionTime);
        $datasetSchema->keywords([$nucleus, $solvent, $dimension.'D', $experiment]);
        $datasetSchema->license($study->license->url);
        $datasetSchema->name($dataset->name);
        $datasetSchema->url(env('APP_URL').'/'.explode(':', $dataset->identifier ? $dataset->identifier : ':')[1]);
        $datasetSchema->datePublished($dataset->release_date);
        $datasetSchema->includedInDataCatalog($dataCatalog);
        $datasetSchema->measurementTechnique('https://terminology.nfdi4chem.de/ts/ontologies/chmo/terms?iri=http%3A%2F%2Fpurl.obolibrary.org%2Fobo%2FCHMO_0000591');
        $datasetSchema->dateCreated($dataset->created_at);
        $datasetSchema->dateModified($dataset->updated_at);
        $datasetSchema->isAccessibleForFree(true);
        $projectSchema = $this->project($project);
        $datasetSchema->isPartOf([$projectSchema]);

        return $datasetSchema;
    }

    /**
     * Implement Bioschemas' study, including the sample and molecules, along
     * with the project it belongs to and, briefly, the datasets it contains.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Study  $study
     * @return object $studySchema
     */
    public function study($study)
    {
        $tags = [];
        foreach ($study->tags as &$tag) {
            $tag = $tag->name;
            array_push($tags, $tag);
        }

        $datasets = [];
        foreach ($study->datasets as &$dataset) {
            $datasetSchema = $this->datasetLite($dataset);
            array_push($datasets, $datasetSchema);
        }

        $creativeWorkSample = Schema::creativeWork();
        $creativeWorkSample['@id'] = 'https://bioschemas.org/types/BioSample/0.1-RELEASE-2019_06_19';
        $SampleconfromsTo = [];
        $SampleconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkSample;

        $sample = $study->sample;

        $creativeWorkMolecule = Schema::creativeWork();
        $creativeWorkMolecule['@id'] = 'https://bioschemas.org/profiles/MolecularEntity/0.5-RELEASE';
        $MoleculeconfromsTo = [];
        $MoleculeconfromsTo['http://purl.org/dc/terms/conformsTo'] = $creativeWorkMolecule;

        $molecules = [];
        foreach ($sample->molecules as &$molecule) {
            $inchiKey = $molecule->INCHI_KEY;
            $pubchemLink = 'https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/inchikey/'.$inchiKey.'/property/IUPACName/JSON';
            $json = file_get_contents($pubchemLink);
            $jsonDecode = json_decode($json, true);
            $cid = $jsonDecode['PropertyTable']['Properties'][0]['CID'];
            $iupacName = $jsonDecode['PropertyTable']['Properties'][0]['IUPACName'];

            $moleculeSchema = Schema::MolecularEntity();
            $moleculeSchema['@id'] = $inchiKey;
            $moleculeSchema['dct:conformsTo'] = $MoleculeconfromsTo;
            $moleculeSchema->name($molecule->CAS_NUMBER);
            $moleculeSchema->url('https://pubchem.ncbi.nlm.nih.gov/compound/'.$cid);
            $moleculeSchema->inChI($molecule->STANDARD_INCHI);
            $moleculeSchema->iupacName($iupacName);
            $moleculeSchema->molecularFormula($molecule->FORMULA);
            $moleculeSchema->molecularWeight($molecule->MOLECULAR_WEIGHT);
            $moleculeSchema->smiles([$molecule->SMILES, $molecule->SMILES_CHIRAL, $molecule->CANONICAL_SMILES]);
            $moleculeSchema->hasRepresentation($molecule->MOL);
            array_push($molecules, $moleculeSchema);
        }
        $sampleSchema = BioSchema::BioSample();
        $sampleSchema['@id'] = $sample->uuid;
        $sampleSchema['dct:conformsTo'] = $SampleconfromsTo;
        $sampleSchema->name($sample->name);
        $sampleSchema->description($sample->description);
        $sampleSchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $sampleSchema->hasBioChemEntityPart($molecules);

        $studySchema = BioSchema::Study();
        $studySchema['@id'] = $study->uuid;
        $studySchema['dct:conformsTo'] = $this->conforms();
        $studySchema->datePublished($study->release_date);
        $studySchema->description($study->description);
        $studySchema->name($study->name);
        $studySchema->studyDomain('Chemistry');
        $studySchema->studySubject('Small molecules');
        $studySchema->about($sampleSchema);
        $studySchema->dateCreated($study->created_at);
        $studySchema->keywords($tags);
        $studySchema->url(env('APP_URL').'/'.explode(':', $study->identifier ? $study->identifier : ':')[1]);
        $studySchema->license($study->license->url);
        $studySchema->hasPart($datasets);

        return $studySchema;
    }

    /**
     * Implement Bioschemas' project along with brief details about
     * the studies and datasets it contains.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  App\Models\Project  $project
     * @return object $projectSchema
     */
    public function project($project)
    {
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

        $citations = [];
        foreach ($project->citations as &$citation) {
            $citationSchema = Schema::creativeWork();
            $citationSchema->abstract($citation->abstract);
            $citationSchema->author($citation->authors);
            $citationSchema->headline($citation->title);
            $citationSchema->identifier($citation->doi);
            array_push($citations, $citationSchema);
        }

        $tags = [];
        foreach ($project->tags as &$tag) {
            $tag = $tag->name;
            array_push($tags, $tag);
        }
        $studies = [];
        foreach ($project->studies as &$study) {
            $datasets = [];
            foreach ($study->datasets as &$dataset) {
                $datasetSchema = $this->datasetLite($dataset);
                array_push($datasets, $datasetSchema);
            }
            $studySchema = $this->studyLite($study);
            array_push($studies, $studySchema);
        }

        $projectSchema = BioSchema::Study();
        $projectSchema['@id'] = $project->uuid;
        $projectSchema['dct:conformsTo'] = $this->conforms();
        $projectSchema['author'] = $authors;
        $projectSchema->datePublished($project->release_date);
        $projectSchema->description($project->description);
        $projectSchema->name($project->name);
        $projectSchema->studyDomain('Chemistry');
        $projectSchema->studySubject('Small molecules');
        $projectSchema['citation'] = $citations;
        $projectSchema->dateCreated($project->created_at);
        $projectSchema->keywords($tags);
        $projectSchema->url(env('APP_URL').'/'.explode(':', $project->identifier ? $project->identifier : ':')[1]);
        $projectSchema->license($project->license->url);
        $projectSchema->hasPart($studies);

        return $projectSchema;
    }

/**
 * Implement Bioschemas upon request by model name to generate a project, study, or dataset schema.
 *
 * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
 * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
 * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
 * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
 *
 * @param  Illuminate\Http\Request  $request
 * @param  App\Models\User  $username
 * @param  App\Models\Project  $projectName
 * @param  App\Models\Study  $studyName Optional
 * @param  App\Models\Dataset  $datasetName Optional
 * @return object
 */
public function modelSchemaByName(Request $request, $username, $projectName, $studyName = null, $datasetName = null)
{
    $user = User::where('username', $username)->firstOrFail();
    if ($user) {
        $project = Project::where([['slug', $projectName], ['owner_id', $user->id]])->firstOrFail();
    }
    if ($project) {
        $projectSchema = $this->project($project);

        if ($studyName) {
            // send study back with project info added to it\
            $study = Study::where([['slug', $studyName], ['owner_id', $user->id], ['project_id', $project->id]])->firstOrFail();
            if ($study) {
                $studySchema = $this->study($study);

                $projectSchema->relatedStudy($studySchema);
                if ($datasetName) {
                    $dataset = Dataset::where([['slug', $datasetName], ['owner_id', $user->id], ['project_id', $project->id], ['study_id', $study->id]])->firstOrFail();
                    // send dataset with project and study details
                    if ($dataset) {
                        $datasetSchema = $this->dataset($dataset, $study, $project);

                        return $datasetSchema;
                    }
                }
            }
        }

        return $projectSchema;
    }
}

    /**
     * Implement Bioschemas upon request by model id to generate a project, study, or dataset schema.
     *
     * @link https://bioschemas.org/profiles/Study/0.2-DRAFT
     * @link https://bioschemas.org/profiles/Dataset/1.0-RELEASE
     * @link https://bioschemas.org/profiles/Sample/0.2-RELEASE-2018_11_10
     * @link https://bioschemas.org/types/MolecularEntity/0.3-RELEASE-2019_09_02
     *
     * @param  Illuminate\Http\Request  $request
     * @param  string  $identifier
     * @return object
     */
    public function modelSchemaByID(Request $request, $identifier)
    {
        $resolvedModel = resolveIdentifier($identifier);
        $namespace = $resolvedModel['namespace'];
        $model = $resolvedModel['model'];

        if ($namespace == 'Project') {
            $projectSchema = $this->project($model);

            return $projectSchema;
        } elseif ($namespace == 'Study') {
            $studySchema = $this->study($model);

            return $studySchema;
        } elseif ($namespace == 'Dataset') {
            $project = $model->project;
            $study = $model->study;
            $datasetSchema = $this->dataset($model, $study, $project);

            return $datasetSchema;
        }
    }
}

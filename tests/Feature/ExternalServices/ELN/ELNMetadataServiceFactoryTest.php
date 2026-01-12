<?php

namespace Tests\Feature\ExternalServices\ELN;

use App\Services\ELN\ChemotionMetadataService;
use App\Services\ELN\ELNMetadataExtractorInterface;
use App\Services\ELNMetadataServiceFactory;
use InvalidArgumentException;
use Tests\TestCase;

class ELNMetadataServiceFactoryTest extends TestCase
{
    public function test_factory_creates_chemotion_service(): void
    {
        $service = ELNMetadataServiceFactory::create('chemotion');

        $this->assertInstanceOf(ChemotionMetadataService::class, $service);
        $this->assertInstanceOf(ELNMetadataExtractorInterface::class, $service);
        $this->assertEquals('chemotion', $service->getELNType());
    }

    public function test_factory_creates_chemotion_service_case_insensitive(): void
    {
        $service1 = ELNMetadataServiceFactory::create('CHEMOTION');
        $service2 = ELNMetadataServiceFactory::create('Chemotion');
        $service3 = ELNMetadataServiceFactory::create('ChEmOtIoN');

        $this->assertInstanceOf(ChemotionMetadataService::class, $service1);
        $this->assertInstanceOf(ChemotionMetadataService::class, $service2);
        $this->assertInstanceOf(ChemotionMetadataService::class, $service3);
    }

    public function test_factory_throws_exception_for_unsupported_eln(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported ELN type: unsupported_eln');

        ELNMetadataServiceFactory::create('unsupported_eln');
    }

    public function test_get_supported_eln_types_returns_array(): void
    {
        $types = ELNMetadataServiceFactory::getSupportedELNTypes();

        $this->assertIsArray($types);
        $this->assertContains('chemotion', $types);
        $this->assertNotEmpty($types);
    }

    public function test_is_supported_returns_true_for_supported_types(): void
    {
        $this->assertTrue(ELNMetadataServiceFactory::isSupported('chemotion'));
        $this->assertTrue(ELNMetadataServiceFactory::isSupported('CHEMOTION'));
        $this->assertTrue(ELNMetadataServiceFactory::isSupported('Chemotion'));
    }

    public function test_is_supported_returns_false_for_unsupported_types(): void
    {
        $this->assertFalse(ELNMetadataServiceFactory::isSupported('unsupported'));
        $this->assertFalse(ELNMetadataServiceFactory::isSupported(''));
        $this->assertFalse(ELNMetadataServiceFactory::isSupported('random_eln'));
    }

    public function test_supported_types_are_lowercase(): void
    {
        $types = ELNMetadataServiceFactory::getSupportedELNTypes();

        foreach ($types as $type) {
            $this->assertEquals(strtolower($type), $type);
        }
    }

    public function test_created_service_has_required_interface_methods(): void
    {
        $service = ELNMetadataServiceFactory::create('chemotion');

        $this->assertTrue(method_exists($service, 'extractMolecules'));
        $this->assertTrue(method_exists($service, 'extractAnalyses'));
        $this->assertTrue(method_exists($service, 'extractAllMetadata'));
        $this->assertTrue(method_exists($service, 'validateMetadata'));
        $this->assertTrue(method_exists($service, 'getELNType'));
        $this->assertTrue(method_exists($service, 'extractAnalysesFromDraft'));
        $this->assertTrue(method_exists($service, 'validateMetadataFromDraft'));
    }

    public function test_factory_returns_new_instance_each_time(): void
    {
        $service1 = ELNMetadataServiceFactory::create('chemotion');
        $service2 = ELNMetadataServiceFactory::create('chemotion');

        $this->assertNotSame($service1, $service2);
    }

    public function test_factory_empty_string_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ELNMetadataServiceFactory::create('');
    }

    public function test_all_supported_types_can_be_created(): void
    {
        $types = ELNMetadataServiceFactory::getSupportedELNTypes();

        foreach ($types as $type) {
            $service = ELNMetadataServiceFactory::create($type);
            $this->assertInstanceOf(ELNMetadataExtractorInterface::class, $service);
        }
    }
}

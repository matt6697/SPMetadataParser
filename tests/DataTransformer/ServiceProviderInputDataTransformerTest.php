<?php
// tests/DataTransformer/ServiceProviderInputDataTransformerTest.php
namespace App\Tests\DataTransformer;

use App\DataTransformer\ServiceProviderInputDataTransformer;
use App\Entity\ServiceProvider;
use App\Dto\ServiceProviderInput;
use App\Exception\MetadataNotFoundException;
use PHPUnit\Framework\TestCase;

class ServiceProviderInputDataTransformerTest extends TestCase
{
  public function testSupportsDTOTransformation()
  {
    $dataTransformer = new ServiceProviderInputDataTransformer();
    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $context['input']['class'] = ServiceProviderInput::class;
    $this->assertTrue($dataTransformer->supportsTransformation($sp_input_dto, ServiceProvider::class, $context));
  }

  public function testSupportsServiceProviderTransformation()
  {
    $dataTransformer = new ServiceProviderInputDataTransformer();
    $sp = new ServiceProvider();
    $this->assertFalse($dataTransformer->supportsTransformation($sp, ServiceProvider::class));
  }

  //Testing autocmpletion of shibboleth service provider metadata endpoint when a shibboleth host is provided
  //Testing LightSAML metadata parser
  public function testShibbolethHost()
  {
    $dataTransformer = new ServiceProviderInputDataTransformer();

    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $sp_input_dto->shibboleth_host = "itservices01.stanford.edu";

    $sp = $dataTransformer->transform($sp_input_dto, "");
    $this->assertEquals('https://itservices01.stanford.edu/shibboleth', $sp->entityId);
  }

  //As https://www.google.com/Shibboleth.sso/Metadata does not exists, a MetadataNotFoundException shoud be raised
  public function testShibbolethHostWrongXMLException()
  {
    $this->expectException(MetadataNotFoundException::class);

    $dataTransformer = new ServiceProviderInputDataTransformer();

    //www.google.com is not using Shibboleth
    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $sp_input_dto->shibboleth_host = "www.google.com";

    $sp = $dataTransformer->transform($sp_input_dto, "");
  }

  //As unresolvable.local is not a resolvable DNS entrys, a MetadataNotFoundException shoud be raised
  public function testShibbolethWrongHostException()
  {
    $this->expectException(MetadataNotFoundException::class);

    $dataTransformer = new ServiceProviderInputDataTransformer();

    //unresolvable.local is not a valid FQDN in the network.
    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $sp_input_dto->shibboleth_host = "unresolvable.local";

    $sp = $dataTransformer->transform($sp_input_dto, "");
  }

  //Testing transform function when a service provider metadata endpoint URL is provided
  //Testing LightSAML metadata parser
  public function testMetadataURL()
  {
    $dataTransformer = new ServiceProviderInputDataTransformer();

    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $sp_input_dto->metadata_url = "https://itservices01.stanford.edu/Shibboleth.sso/Metadata";

    $sp = $dataTransformer->transform($sp_input_dto, "");
    $this->assertEquals('https://itservices01.stanford.edu/shibboleth', $sp->entityId);
  }

  //As https://www.google.com is an HTML document and not a valid XML documents, a MetadataNotFoundException shoud be raised
  public function testMetadataURLWrongXMLException()
  {
    $this->expectException(MetadataNotFoundException::class);

    $dataTransformer = new ServiceProviderInputDataTransformer();

    //www.google.com is not using Shibboleth
    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $sp_input_dto->metadata_url = "https://www.google.com";

    $sp = $dataTransformer->transform($sp_input_dto, "");
  }

  //As https://unresolvable.local does not exists, a MetadataNotFoundException shoud be raised
  public function testMetadataURLWrongHostException()
  {
    $this->expectException(MetadataNotFoundException::class);

    $dataTransformer = new ServiceProviderInputDataTransformer();

    //unresolvable.local is not a valid FQDN in the network.
    $sp_input_dto = new \App\Dto\ServiceProviderInput();
    $sp_input_dto->metadata_url = "https://unresolvabsle.local";

    $sp = $dataTransformer->transform($sp_input_dto, "");
  }

  //As no valid metadata extraction option is provided, $metadata_xml_string wil be empty and a MetadataNotFoundException shoud be raised
  public function testEmptyServiceProviderInputData()
  {
    $this->expectException(MetadataNotFoundException::class);

    $dataTransformer = new ServiceProviderInputDataTransformer();

    //Empty input obct.
    $sp_input_dto = new \App\Dto\ServiceProviderInput();

    $sp = $dataTransformer->transform($sp_input_dto, "");
  }
}

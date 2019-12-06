<?php
// src/DataTransformer/ServiceProviderInputDataTransformer.php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\ServiceProvider;
use App\Exception\MetadataNotFoundException;
use LightSaml\Model\Context\DeserializationContext;
use LightSaml\Model\Metadata\Metadata;

final class ServiceProviderInputDataTransformer implements DataTransformerInterface
{  /**
   * {@inheritdoc}
   */
  public function transform($data, string $to, array $context = []) {
    if(isset($data->metadata_url)) {
      $metadata_xml_string = $this->parseUrl($data->metadata_url);
    }
    else if(isset($data->shibboleth_host)) {
      $metadata_xml_string = $this->parseShibbolethHost($data->shibboleth_host);
    }

    if(isset($metadata_xml_string) && simplexml_load_string($metadata_xml_string)) {
      $metadata = $this->parseXmlString($metadata_xml_string);

      if(isset($metadata) && $metadata !== '') {
        $sp = new ServiceProvider();
        $sp->entityId = $metadata->getEntityId();
        $sp->metadata_xml_string = $metadata_xml_string;
        return $sp;
      } else {
        throw new MetadataNotFoundException('No valid SAML2 Metadata found at the provided location.');
      }
    } else {
      throw new MetadataNotFoundException('No valid XML data found at the provided location.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function supportsTransformation($data, string $to, array $context = []): bool {
    // in the case of an input, the value given here is an array (the JSON decoded).
    // if it's a book we transformed the data already
    if ($data instanceof ServiceProvider) {
      return false;
    }

    return ServiceProvider::class === $to && null !== ($context['input']['class'] ?? null);
  }

  private function parseUrl($url) {
    try {
      return file_get_contents($url);
    } catch (\Exception $e) {
      throw new MetadataNotFoundException(sprintf('The provided hostname %s is not a valid SAML2 Service Provider.', $hostname));
    }
  }

  private function parseShibbolethHost($hostname) {
    try {
      return file_get_contents("https://".$hostname."/Shibboleth.sso/Metadata");
    } catch (\Exception $e) {
      throw new MetadataNotFoundException(sprintf('The provided hostname %s is not a valid Shibboleth Service Provider.', $hostname));
    }
  }

  private function parseXmlString($xml_string) {
    $deserializatonContext = new DeserializationContext();
    return \LightSaml\Model\Metadata\Metadata::fromXML($xml_string, $deserializatonContext);
  }
}

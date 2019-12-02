<?php
// src/DataTransformer/ServiceProviderInputDataTransformer.php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\ServiceProvider;
use SimpleSAML\Metadata\SAMLParser;
use SimpleSAML\Utils\HTTP;

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

    if(isset($metadata_xml_string)) {
      $metadata = $this->parseXmlString($metadata_xml_string);

      if(isset($metadata)) {
        $sp = new ServiceProvider();
        $sp->entityId = $metadata->getEntityId();
        $sp->metadata_xml_string = $metadata_xml_string;
        return $sp;
      } else {
        return null;
      }
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
    return \SimpleSAML\Utils\HTTP::fetch($url);
  }

  private function parseShibbolethHost($hostname) {
    return \SimpleSAML\Utils\HTTP::fetch("https://".$hostname."/Shibboleth.sso/Metadata");
  }

  private function parseXmlString($xml_string) {
    return \SimpleSAML\Metadata\SAMLParser::parseString($xml_string);
  }
}

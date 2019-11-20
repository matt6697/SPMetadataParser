<?php declare(strict_types = 1);

namespace Saml2;

use SimpleSAML\Metadata\SAMLParser;
use SimpleSAML\Utils\HTTP;

class ServiceProvider {
  protected $metadata_xml_string;
  protected $metadata;

  private function __construct($metadata_xml_string) {
    $this->metadata_xml_string = $metadata_xml_string;

    //Parse SAML2 XML metadata
    $this->metadata = \SimpleSAML\Metadata\SAMLParser::parseString($this->metadata_xml_string);

    //Check metadata signature

    //Check https certificate of SAML2 service provider (is it signed by a trusted CA ?)
  }

  public static function parseUrl($url) {
    $xml_string = \SimpleSAML\Utils\HTTP::fetch($url);
    return new ServiceProvider($xml_string);
  }

  public static function parseShibbolethHost($hostname) {
    $xml_string = \SimpleSAML\Utils\HTTP::fetch("https://$hostname/Shibboleth.sso/Metadata");
    return new ServiceProvider($xml_string);
  }

  public function getEntityId() {
    return $this->metadata->getEntityId();
  }

  public function getMetadataXMLString() {
    return $this->metadata_xml_string;
  }
}



?>

<?php
require 'vendor/autoload.php';

/*use SimpleSAML\Metadata\SAMLParser;

$metadata = SAMLParser::parseFile('/home/matthieu/SPMetadataParser/sample-metadata/shibboleth.xml');*/

use Saml2\ServiceProvider;
use Saml2\ShibbolethIdentityProvider;

$netscalerSP = Saml2\ServiceProvider::parseUrl('/home/matthieu/SPMetadataParser/sample-metadata/netscaler.xml');
var_dump($netscalerSP->getEntityId());

$shibbolethSP = Saml2\ServiceProvider::parseShibbolethHost("itservices01.stanford.edu");
var_dump($shibbolethSP->getEntityId());

$identity_provider = new Saml2\ShibbolethIdentityProvider("/home/matthieu/idp/metadata", "/home/matthieu/idp/relying-party.xml");
$identity_provider->registerSP($netscalerSP);
?>

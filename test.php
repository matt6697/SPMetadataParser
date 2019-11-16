<?php
require 'vendor/autoload.php';

use SimpleSAML\Metadata\SAMLParser;

$metadata = SAMLParser::parseFile('/home/matthieu/SPMetadataParser/sample-metadata/shibboleth.xml');
var_dump($metadata);
?>

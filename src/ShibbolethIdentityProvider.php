<?php declare(strict_types = 1);

namespace Saml2;

use SimpleSAML\Metadata\SAMLParser;
use SimpleSAML\Utils\HTTP;
use Saml2\ServiceProvider;

class ShibbolethIdentityProvider {
  protected $metadataPath;
  protected $relyingpartyXmlFile;
  protected $registeredServiceProviders;

  public function __construct(string $metadataPath, string $relyingpartyXmlFile) {
    if(is_dir($metadataPath) && file_exists($relyingpartyXmlFile)) {
      $this->metadataPath = $metadataPath;
      $this->relyingpartyXmlFile = $relyingpartyXmlFile;

      //Extract already registered service providers from relying-party.xml file
    }
  }

  public function registerSP(ServiceProvider $SP) {
    var_dump($SP);
    $myfile = fopen("$this->metadataPath/newfile.txt", "w") or die("Unable to open file!");
    if (flock($myfile, LOCK_EX)) {
        fwrite($myfile, $SP->getMetadataXMLString());
        flock($myfile, LOCK_UN); // unlock the file
    } else {
        // flock() returned false, no lock obtained
        print "Could not lock $filename!\n";
    }
    fclose($myfile);
  }
}



?>

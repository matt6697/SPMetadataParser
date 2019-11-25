<?php
namespace App\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Context\SerializationContext;

class MyFormatEncoder implements EncoderInterface, DecoderInterface {
  const FORMAT = 'myformat';

  public function supportsEncoding($format): bool {
    return self::FORMAT === $format;
  }

  public function supportsDecoding($format): bool {
    return self::FORMAT === $format;
  }

  //Sample metadata file : https://metadata.federation.renater.fr/renater/main/main-sps-renater-metadata.xml
  public function encode($data, $format, array $context = []) {
    $saml2Entities = new \LightSaml\Model\Metadata\EntitiesDescriptor();
    $serializationContext = new SerializationContext();
    $document = $serializationContext->getDocument();

    /*Convert each SP xml string into an EntityDescriptor object and add it into the $saml2Entities EntitiesDescriptor
     *which will be used for federation XML metadata file generation.
     */
    foreach ($data as $sp) {
      $entityDescriptor = \LightSaml\Model\Metadata\EntityDescriptor::loadXml($sp['metadata_xml_string']);
      $saml2Entities->addItem($entityDescriptor);
    }

    //Add signature to the generated EntitiesDescriptor object
    //$certificate = \LightSaml\Credential\X509Certificate::fromFile('certificate.crt');
    //$privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey('private.key', '', true);
    //$saml2Entities->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));

    //Serialize the EntitiesDescriptor to XML and return the XML string.
    $saml2Entities->serialize($document, $serializationContext);
    return $document->saveXML();
  }

  public function decode($data, $format, array $context = []) {
    //return Yaml::parse($data);
  }
}

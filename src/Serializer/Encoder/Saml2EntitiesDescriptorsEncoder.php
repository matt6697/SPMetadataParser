<?php
namespace App\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use LightSaml\Model\Metadata\EntitiesDescriptor;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Context\SerializationContext;

class Saml2EntitiesDescriptorsEncoder implements EncoderInterface, DecoderInterface {
  const FORMAT = 'saml2ed';

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

    //Set returned metadata valididy for 1 day = 24 * 60 * 60 seconds
    $saml2Entities->setValidUntil(time() + 24*60*60);

    //Add signature to the generated EntitiesDescriptor object
    $certificate = \LightSaml\Credential\X509Certificate::fromFile(getcwd() . '/../sample-certificate/certificate.pem');
    $privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey(getcwd() . '/../sample-certificate/certificate.key', '', true);
    $saml2Entities->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));

    //Serialize the EntitiesDescriptor to XML and return the XML string.
    $saml2Entities->serialize($document, $serializationContext);
    return $document->saveXML();
  }

  public function decode($data, $format, array $context = []) {
    //return Yaml::parse($data);
  }
}

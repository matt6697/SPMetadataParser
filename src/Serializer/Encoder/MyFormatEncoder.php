<?php
namespace App\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Yaml\Yaml;

class MyFormatEncoder implements EncoderInterface, DecoderInterface {
  const FORMAT = 'myformat';

  public function supportsEncoding($format): bool {
    return self::FORMAT === $format;
  }

  public function supportsDecoding($format): bool {
    return self::FORMAT === $format;
  }

  public function encode($data, $format, array $context = []) {
    return Yaml::dump($data);
  }

  public function decode($data, $format, array $context = []) {
    return Yaml::parse($data);
  }
}

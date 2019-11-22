<?php
// api/src/Entity/ServiceProvider.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Dto\ServiceProviderInput;
use App\Dto\ServiceProviderOutput;

/**
 * @ORM\Entity
 * @ApiResource(
 *   input=ServiceProviderInput::class
 * )
 */
final class ServiceProvider {
  /**
   * @var int The id of this service provider.
   *
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   * @ApiProperty(identifier=true)
   */
  private $id;

  /**
   * @var string The entityId of this SAML2 service provider.
   *
   * @ORM\Column(type="string", unique=true)
   * @Assert\NotBlank
   */
  public $entityId;

  /**
   * @var string The registered XML metadata of this service provider.
   *
   * @ORM\Column(type="text")
   * @Assert\NotBlank
   */
  public $metadata_xml_string;

  public function __construct() {
  }

  public function getId(): ?int {
    return $this->id;
  }
}

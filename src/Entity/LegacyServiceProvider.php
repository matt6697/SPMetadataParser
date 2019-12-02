<?php
// api/src/Entity/LegacyServiceProvider.php

/*
Hack of the CollectionDataProvider to keep bakward compatibility with previous API version using GET instead of POST
to register service providers.
VERY BAD USAGE OF API PLATFORM REQUIRED FOR MIGRATION
MARKED AS DEPRECATED AND TO BE AS SOON AS POSSIBLE AFTER API CLIENTS MIGRATION
*/

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *     deprecationReason="For backward compatibility. Non REST compliant use of HTTP GET parameter to register a new service provider.",
 *     collectionOperations={
 *       "get"={"path"="/shib"}
 *     },
 *     itemOperations={}
 * )
 */
final class LegacyServiceProvider {
  /**
   * @var int The id of this service provider.
   * @ApiProperty(identifier=true)
   */
  private $id;

  /**
   * @var ServiceProviders[]
   */
  public $serviceProviders;

  public function __construct()
  {
      $this->serviceProviders = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }
}

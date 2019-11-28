<?php
// api/src/Entity/ServiceProvider.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *     collectionOperations={
 *       "get"={
 *         "output_formats"={"myformat"}
 *        }
 *     },
 *     itemOperations={}
 * )
 */
final class EntitiesDescriptors {
  /**
   * @var int The id of this service provider.
   * @ApiProperty(identifier=true)
   */
  private $id;

  /**
   * @var Entities[]
   */
  public $entities;

  public function __construct()
  {
      $this->entities = new ArrayCollection();
  }

  public function getId(): ?int {
    return $this->id;
  }
}

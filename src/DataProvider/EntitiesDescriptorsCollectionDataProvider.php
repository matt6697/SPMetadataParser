<?php
// api/src/DataProvider/EntitiesDescriptorsCollectionDataProvider.php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EntitiesDescriptors;
use App\Entity\ServiceProvider;

final class EntitiesDescriptorsCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager) {
    //Use the Doctrine entity manager for database operations
    $this->entityManager = $entityManager;
  }

  public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
    //CollectionDataProvier class is compatible with EntitiesDescriptors operations
    return EntitiesDescriptors::class === $resourceClass;
  }

  public function getCollection(string $resourceClass, string $operationName = null) {
    //Retrieve all the registered service providers from the database
    //The returned object will be transformed by the custom saml2 entities encoder to build
    //the EntitiesDescriptors XML which will be used by the shibboleth IDP
    return $this->entityManager->getRepository("App\Entity\ServiceProvider")->findAll();
  }
}

<?php
// api/src/DataProvider/LegacyServiceProviderCollectionDataProvider.php

/*
Hack of the CollectionDataProvider to keep bakward compatibility with previous API version using GET instead of POST
to register service providers.
VERY BAD USAGE OF API PLATFORM REQUIRED FOR MIGRATION
MARKED AS DEPRECATED AND TO BE AS SOON AS POSSIBLE AFTER API CLIENTS MIGRATION
*/

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\LegacyServiceProvider;
use App\Entity\ServiceProvider;
use \App\Dto\ServiceProviderInput;
use App\DataTransformer\ServiceProviderInputDataTransformer;

final class LegacyServiceProviderCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface {
  private $requestStack;
  private $entityManager;

  public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager) {
    $this->requestStack = $requestStack;
    $this->entityManager = $entityManager;
  }

  public function supports(string $resourceClass, string $operationName = null, array $context = []): bool {
    //CollectionDataProvier class is compatible with EntitiesDescriptors operations
    return LegacyServiceProvider::class === $resourceClass;
  }

  public function getCollection(string $resourceClass, string $operationName = null) {
    $query = $this->requestStack->getCurrentRequest()->query;
    $hostname = $query->get('sp');
    if(isset($hostname)) {
      //Create a new service provider object from query parameter
      $sp_input_dto = new \App\Dto\ServiceProviderInput();
      $sp_input_dto->shibboleth_host = $hostname;
      $sp = (new \App\DataTransformer\ServiceProviderInputDataTransformer())->transform($sp_input_dto, "");

      //Save service provider in database which will autogenerate the ServiceProvider $id
      $this->entityManager->persist($sp);
      $this->entityManager->flush();

      //Return an array containing the created service provider
      return [$sp];
    }
  }
}

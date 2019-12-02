<?php
// src/Dto/ServiceProviderInput.php

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

final class ServiceProviderInput {
  /**
   * @ApiProperty(
   *     attributes={
   *         "openapi_context"={
   *             "type"="string",
   *             "example"="https://itservices01.stanford.edu/Shibboleth.sso/Metadata"
   *         }
   *     }
   * )
   */
  public $metadata_url;

  /**
   * @ApiProperty(
   *     attributes={
   *         "openapi_context"={
   *             "type"="string",
   *             "example"="itservices01.stanford.edu"
   *         }
   *     }
   * )
   */
  public $shibboleth_host;
}

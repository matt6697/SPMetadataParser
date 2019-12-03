# SPMetadataParser
## Getting started
### Installation
Clone the git repository and run `composer install`.

As the project is based on **Api Platform** and **Symfony 4**, additionnal PHP modules might be required by composer (curl, mbstring, xml,...). Install the required PHP modules and run `composer install` again.

### Database configuration
SPMetadataParser support multiple database backends :
 - SQlite
 - Mysql

Install the choosen PHP database module and configure database access in `.env` file.

SPMetadataParser is configured by default to use a SQlite database stored in `%kernel.project_dir%/var/metadata.db`.
`
```
DATABASE_URL=sqlite:///%kernel.project_dir%/var/metadata.db  
```

Initialize the database structure with symfony console before starting the web server.

```
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:create
```

### API Usage
#### Registering a Shibboleth Service Provider

#### Registering a Non-Shibboleth SAML2 Service Provider
The URL of the SAML2 Service Provider Metadata endpoint is used for registration.
This URL may vary depending on the SAML2 service provider technology.

```
curl -X POST "https://<fqdn>/api/service_providers" -H  "accept: application/ld+json" -H  "Content-Type: application/ld+json" -d "{\"metadata_url\":\"https://itservices01.stanford.edu/Shibboleth.sso/Metadata\"}"
```

## Shibboleth 2 documentation
### Type of Relying Parties
In nearly all cases an IdP communicates with a service provider. However, in some more advanced cases an IdP may communicate with other entities (like other IdPs). The IdP configuration uses the generic term relying party to describe any peer with which it communicates. A service provider, then, is simply the most common type of relying party.

The IdP recognizes three classifications of relying parties:
  - **anonymous** - a relying party for which the IdP has no metadata
  - **default** - a relying party for which the IdP does have metadata but for which there is no specific configuration
  - **specified** - a relying party for which the IdP has metadata and a specific configuration

  The configuration for each type of relying party is given by their respective configuration elements: `<AnonymousRelyingParty>`, `<DefaultRelyingParty>`, and `<RelyingParty>`.
Clone the git repository and run `composer install`.
Additionnal PHP modules might be required (curl, mbstring, xml,...) by composer before installing Symfony

### Metadata and Relying Parties
The IdP uses metadata to drive a significant portion of its internal communication logic with a relying party. The metadata contains information such as what keys to use, whether certain information needs to be digitally signed, which protocols are supported, etc. A relying party is identified within metadata by an <EntityDescriptor> element with an entityID attribute whose value corresponds to the relying party's entity ID. Entities may be grouped within an <EntitiesDescriptor> element and this group may be given a name by means of the name attribute. Entity groups may be nested.

When creating a specified relying party configuration you may specify either a specific entity or a group of entities. In that event that there is overlap the most specific configuration is used, no settings are "inherited" because of this overlap. As was mentioned above, a relying party for which the IdP can find no metadata is termed an anonymous relying party.

https://wiki.shibboleth.net/confluence/display/SHIB2/IdPUnderstandingRP


## Webservice architecture

## Administrative actions
**Clear the API Platform cache**
```
php bin/console cache:clear
```

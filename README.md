# SPMetadataAPI
## Introduction
SPMetadataAPI is a SAML2 federation operator webservice based on API Platform and LightSAML PHP library:
 * Providing SAML2 **metadata registration** capabilities.
 * Validating SAML2 metadata structure using LightSAML parser.
 * Acting a SAML2 **metadata aggregator** HTTP server for Shibboleth identity providers.

 ![SAML2 service provider registration](doc/images/metadataoperator.jpg)

 * [Webservice architecture](/doc/architecture.md)
 * [Shibboleth integration](doc/shibboleth.md)

## Getting started
### Installation
Clone the git repository and run `composer install`.

As the project is based on **Api Platform** and **Symfony 4**, additionnal PHP modules might be required by composer (curl, mbstring, xml,...). Install the required PHP modules and run `composer install` again.

### Database configuration
SPMetadataParser supports multiple database backends :
 - SQlite
 - MySql

Install the choosen PHP database module and configure database URL as an environment variable :
  * Configure the `.env` file for a standalone web-server deployment.
  * Use Docker environment variables for a containairized deployment.

SPMetadataParser is configured by default to use a SQlite database stored in `%kernel.project_dir%/var/metadata.db`.

```
DATABASE_URL=sqlite:///%kernel.project_dir%/var/metadata.db  
```

Initialize the database structure with symfony console before starting the web server.

```
$ bin/console doctrine:database:create
$ bin/console doctrine:schema:create
```

### Administrative actions
#### Clear the API Platform cache
The Api Platform cache can be cleared using the following Symfony 4 console command :
```
php bin/console cache:clear
```

## API Usage
### Registering a Shibboleth Service Provider
The **FQDN** of the Shibboleth Service Provider Metadata endpoint is used for registration. Webservice will automatically construct the URL of the Shibboleth Service Provider metadata endpoint `https://<FQDN>/Shibboleth.sso/Metadata`

```
curl -X POST "http://<webservice>/api/service_providers" \
     -H  "accept: application/ld+json" \
     -H  "Content-Type: application/ld+json" \
     -d "{\"shibboleth_host\":\"itservices01.stanford.edu\"}"
```

### Registering a Non-Shibboleth SAML2 Service Provider
The **URL** of the SAML2 Service Provider Metadata endpoint is used for registration.
This URL may vary depending on the SAML2 service provider technology.

```
curl -X POST "https://<webservice>/api/service_providers" \
     -H  "accept: application/ld+json" \
     -H  "Content-Type: application/ld+json" \
     -d "{\"metadata_url\":\"https://itservices01.stanford.edu/Shibboleth.sso/Metadata\"}"
```

### Backward compatibility registration using GET HTTP request
Shibboleth-only service providers can be registered using the **/shib?sp=<fqdn>** API endpoint. This API endpoint is *deprecated** and has been included for backward compatibility only. It will be removed in the next major release.
```
curl https://<webservice>/api/shib?sp=itservices01.stanford.edu
```


### Gathering the EntitiesDescriptors aggregated XML metadata container
Aggregated SAML2 Metadata is provided to the Shibboleth IDP accessing the webservice using a [FileBackedHTTPMetadataProvider](shibboleth.md) through the `GET /entities_descriptors` API endpoint.

```
curl https://<webservice>/api/entities_descriptors
```

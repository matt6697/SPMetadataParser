# Webservice architecture
## Overview
### Why a SAML2 metadata webservice ?
SAML2 Services Providers can be registered in a Shibboleth Identity Provider using various methods :
  * Direct edit of IDP XML configuration files (relying-party.xml, metadata files)
  * Through an file backed remote HTTP metadata provider.
  * Using a [MDQ metadata provider](https://wiki.shibboleth.net/confluence/display/SP3/MDQMetadataProvider) loading metadata on demand from an HTTP server using the conventions defined by the Metadata Query Protocol draft specification.

Direct edit of IDP XML configuration files has been described in various blog. However, this architecture is strongly discouraged in a production environment because of the following limitations :
  * High risk of XML files corruption.
  * Direct impact of registration load on IDP resources.
  * Limited horizontal scalability.

### Service provider registration
![SAML2 service provider registration](images/registration.jpg)

### Integration with a Shibboleth IDP
![Linking the webservice with Shibboleth IDP metadata provider](images/authentication.jpg)


## API Platform integration
### Registering SAML2 service providers
#### POST /servicesproviders request handling
The `POST /serviceproviders` endpoint is the main SAML2 Service Provider registration endpoint. Input data is either :
  * A **metadata_url** representing the URL of the service provider metadata endpoint.
  * A **shibboleth_host** representing the fully qualified name (FQDN) of a Shibboleth service provider. In this case, the URL of the service provider metadata endpoint is automatically constructed by the webservice using the `https://<shibboleth_host>/Shibboleth.sso/Metadata` pattern.

API Platform Data Transformer Object (DTO) is used to have a different representation of the ServiceProvider class as input (POST) than the one describing the data objects stored in the database or returned as a response of a GET request.

The `ServiceProviderInput` class describes the input object. The `ServiceProviderInputDataTransformer` class converts the `ServiceProviderInput` into a `ServiceProvider` object by :
  * Querying the SAML2 Service Provider metadata endpoint using either the **metadata_url** or the **https://<shibboleth_host>/Shibboleth.sso/Metadata** URL.
  * Parsing and validating the gathered XML string using the **LightSAML** SAML2 metadata parser.
  * Extracting the SAML2 Service Provider **entityId** from the parsed metadata.

![SAML2 service provider registration](images/post-serviceproviders.jpg)

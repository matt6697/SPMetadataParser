# Shibboleth integration
## Relying Parties
In nearly all cases a Shibboleth IDP communicates with a service provider. However, in some more advanced cases an IdP may communicate with other entities (like other IdPs). The IdP configuration uses the generic term relying party to describe any peer with which it communicates. A service provider, then, is simply the most common type of relying party.

The IdP recognizes three classifications of relying parties:
  - **anonymous** - a relying party for which the IdP has no metadata
  - **default** - a relying party for which the IdP does have metadata but for which there is no specific configuration
  - **specified** - a relying party for which the IdP has metadata and a specific configuration

The configuration for each type of relying party is given by their respective configuration elements: `<AnonymousRelyingParty>`, `<DefaultRelyingParty>`, and `<RelyingParty>`.

## Metadata and Relying Parties
The IdP uses metadata to drive a significant portion of its internal communication logic with a relying party. The metadata contains information such as what keys to use, whether certain information needs to be digitally signed, which protocols are supported, etc. A relying party is identified within metadata by an <EntityDescriptor> element with an entityID attribute whose value corresponds to the relying party's entity ID. Entities may be grouped within an <EntitiesDescriptor> element and this group may be given a name by means of the name attribute. Entity groups may be nested.

When creating a specified relying party configuration you may specify either a specific entity or a group of entities. In that event that there is overlap the most specific configuration is used, no settings are "inherited" because of this overlap. As was mentioned above, a relying party for which the IdP can find no metadata is termed an anonymous relying party.

## Metadata webservice integration with a Shibboleth 3 IDP
Shibboleth 3 IDP can be linked to the metadata webservice using the **FileBackedHTTPMetadataProvider** which loads a metadata file from a remote HTTP server. The provider periodically reloads the metadata file if necessary.

**The metadata is loaded (and reloaded) out-of-band, independent of normal IDP operation, and therefore will not interfere with any SAML protocol exchange.**

The frequency of metadata refresh is influenced by the Reloading Attributes. In particular, the `minRefreshDelay` and `maxRefreshDelay` attributes strongly influence the frequency of metadata refresh. Any `cacheDuration` and `validUntil` attributes in the metadata itself also influence the process.

```
<!--
    Load (and reload) a signed metadata aggregate from a remote HTTP server.

    This sample configuration assumes: (1) the top-level element of the XML
    document is signed; (2) the top-level element of the XML document is
    decorated with a validUntil attribute; (3) the validity interval is two
    weeks (P14D) in duration; and (4) the server supports HTTP conditional GET.

    The metadata refresh process is influenced by the configured values of
    the minRefreshDelay attribute (default: PT30S) and the maxRefreshDelay
    attribute (default: PT4H) and also by any cacheDuration and validUntil
    attributes in the metadata itself. If the server does not support HTTP
    conditional GET, the attributes should be adjusted accordingly.
-->
<MetadataProvider id="RemoteMetadataAggregate" xsi:type="FileBackedHTTPMetadataProvider"
                  backingFile="%{idp.home}/metadata/federation-metadata-copy.xml"
                  metadataURL="http://example.org/metadata/federation-metadata.xml">

    <!--
        Verify the signature on the root element of the metadata aggregate
        using a trusted metadata signing certificate.
    -->
    <MetadataFilter xsi:type="SignatureValidation" requireSignedRoot="true"
        certificateFile="%{idp.home}/conf/metadata/md-cert.pem"/>

    <!--
        Require a validUntil XML attribute on the root element and
        make sure its value is no more than 14 days into the future.
    -->
    <MetadataFilter xsi:type="RequiredValidUntil" maxValidityInterval="P14D"/>

    <!-- Consume all SP metadata in the aggregate -->
    <MetadataFilter xsi:type="EntityRoleWhiteList">
        <RetainedRole>md:SPSSODescriptor</RetainedRole>
    </MetadataFilter>      

</MetadataProvider>
```

_Reference_ : https://wiki.shibboleth.net/confluence/display/IDP30/FileBackedHTTPMetadataProvider

## Metadata webservice integration with a Shibboleth 2 IDP
The File Backed HTTP Metadata Provider works like the HTTP Metadata Provider, but stores metadata in a local temporary file upon successful retrieval. This protects against the case where metadata has been successfully fetched once but the remote server is offline during subsequent requests (even across IdP reboots).

**Note, as of version 2.2, this metadata provider supports deflate and gzip compression as well as conditional fetching of metadata based on Last-Modified and ETag headers.**

It is defined by the element `<MetadataProvider xsi:type="FileBackedHTTPMetadataProvider" xmlns="urn:mace:shibboleth:2.0:metadata">` with the following required attributes:
  * **id** - provides a unique, amongst metadata providers, identifier that may be used to reference the provider
  * **metadataURL** - the URL of the metadata file
  * **backingFile** - the filesystem path where a backup copy of the metadata file is stored

and the following optional attributes:
  * **failFastInitialization (added in v2.2)** - a boolean flag indicating whether, during startup, the IdP should fail to start if there is a problem fetching metadata (default value: true)
  * **requireValidMetadata (added in v2.2)** - a boolean flag that indicates that loaded metadata must be valid, according to the validUntil attributes found in the metadata (default value: true)
  * **maintainExpiredMetadata (deprecated in v2.2, use requireValidMetadata instead)** - A boolean flag indicating whether metadata should be retained, and used, after it has expired and the defined URL can not be reached to retrieve fresh metadata, defaults to false
  * **refreshDelayFactor (added in v2.2)** - an number between 0.0 and 1.0, exclusive, used to determine the next metadata refresh cycle based on the current metadata's cache expiration time (default value: 0.75)
  * **minRefreshDelay (added in v2.2)** - a lower bound on the frequency of metadata refresh cycles given in XML duration notation (default value: PT5M)
  * **maxRefreshDelay (added in v2.2)** - an upper bound on the frequency of metadata refresh cycles given in XML duration notation (default value: PT4H)
  * **cacheDuration (deprecated in v2.2, use maxRefreshDelay instead)** - Maximum length of time, in seconds, metadata should be cached for, defaults to 2880. If the metadata file expressed a shorter duration, either through an explicit expiration date or by means of a cache duration, then the metadata specified duration will take precedence
  * **disregardSslCertificate (added in v2.2)** - boolean flag indicating whether the servers SSL certificate should always be accepted regardless of whether its valid (defaults value: false)
  * **requestTimeout** - Maximum length of time to wait for the remote server to finish its response given in XML duration notation (default value: PT5S). For versions prior to v2.2, use value in milliseconds.
  * **proxyHost (added in v2.2)** - hostname of the HTTP proxy to use when fetching metadata
  * **proxyPort (added in v2.2)** - port of the HTTP proxy to use when fetching metadata
  * **proxyUser (added in v2.2)** - username used when connecting to the HTTP proxy to use when fetching metadata
  * **proxyPassword (added in v2.2)** - password used when connecting to the HTTP proxy to use when fetching metadata
  * **basicAuthUser (added in v2.2)** - HTTP BASIC authentication username used when connecting to the HTTP proxy to use when fetching metadata
  * **basicAuthPassword (added in v2.2)** - HTTP BASIC authentication password used when connecting to the HTTP proxy to use when fetching metadata

```
<!-- RelyingParty elements above this point -->
<MetadataProvider xsi:type="FileBackedHTTPMetadataProvider" xmlns="urn:mace:shibboleth:2.0:metadata"
                  id="MyMetadata"
                  metadataURL="http://example.org/metadata.xml"
                  backingFile="/tmp/idp-metadata.xml" />
```

_Reference_ : https://wiki.shibboleth.net/confluence/display/SHIB2/IdPMetadataProvider#IdPMetadataProvider-FileBackedHTTPMetadataProvider

## Resources
https://wiki.shibboleth.net/confluence/display/SHIB2/IdPUnderstandingRP

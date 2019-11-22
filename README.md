# SPMetadataParser

## Shibboleth 2 documentation
### Type of Relying Parties
In nearly all cases an IdP communicates with a service provider. However, in some more advanced cases an IdP may communicate with other entities (like other IdPs). The IdP configuration uses the generic term relying party to describe any peer with which it communicates. A service provider, then, is simply the most common type of relying party.

The IdP recognizes three classifications of relying parties:
  - **anonymous** - a relying party for which the IdP has no metadata
  - **default** - a relying party for which the IdP does have metadata but for which there is no specific configuration
  - **specified** - a relying party for which the IdP has metadata and a specific configuration

  The configuration for each type of relying party is given by their respective configuration elements: `<AnonymousRelyingParty>`, `<DefaultRelyingParty>`, and `<RelyingParty>`.

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

LightSAML SP Bundle
===================

[![License](https://img.shields.io/packagist/l/lightsaml/sp-bundle.svg)](https://packagist.org/packages/lightsaml/sp-bundle)
[![Build Status](https://travis-ci.org/lightSAML/SpBundle.svg?branch=master)](https://travis-ci.org/lightSAML/SpBundle)
[![Coverage Status](https://coveralls.io/repos/lightSAML/SpBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/lightSAML/SpBundle?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/lightsaml/sp-bundle.svg?style=flat)](http://hhvm.h4cc.de/package/lightsaml/sp-bundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/928197ea-1587-4da5-b9ea-6a0e53eb8924/mini.png)](https://insight.sensiolabs.com/projects/928197ea-1587-4da5-b9ea-6a0e53eb8924)
[![Slack](https://img.shields.io/badge/slack--green.svg?style=social&logo=data%3Aimage%2Fpng%3Bbase64%2CiVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAADAFBMVEUAAAA4Dzg%2BETo6M0o9amdKGUJFN09GUl0ck30dlH5lhh1xiBpoiCFpkTsimIEkmYcnm4cxqYk%2Fq4o0o5Y6so1AtY9AsJFAuZBAuZFFupNFr61qqoVvo5ZtuKp%2BvKhTwJxawqFgxaRpxqhqyKlhwcxuwshtyNlvyNpvytxxxMNwyduDEkyoHFu9FVvMDgzPDhrPGw%2FSFSbXEj3TNBPXLS3FE1zdE1ndFWHfFGPfFmTgGGTgHmrjK3LiMHXjNXnbXBXgeh3mZ3PqZJjraJrob57peILtd6WAjyS8nB2In06%2FtmXRoh3nnh3jjiPpqB7pqR%2FjqCTqrSnqtD%2FfvF7tt0bvu1fvvVPxxWvxyXTyy3mQoKCM1LyQ1b%2BM09%2BZ2cSV1diB0OGF0uKP1uSU1%2BWb2uap3OOq4Oqz4uyy4u301I8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAB6Dz29AAABAHRSTlP%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8AU%2FcHJQAAAAlwSFlzAAALEgAACxIB0t1%2B%2FAAAABh0RVh0U29mdHdhcmUAcGFpbnQubmV0IDQuMC45bDN%2BTgAAAMJJREFUKFNj%2BA8F4REQGiaQ6ecHYQAF4uRBjABvr9QkiICihDKQ4cnNq6qRCBaIERGLT1CX5uKRS4Go0OTjlNLQkHX3CAZpZUjTUuHnEIjN%2BB%2Fk5w8WSNZQExYUAbIiIfYw%2FE%2F%2F%2F19CIhrIdLA3MbdwA7tDQULpv5OtoZ6%2BmSXI0P%2F%2Fo1jYdC0sjAyMHcG2%2FLfTYWLUtrB29fUJBQvYWJiyMrMDJUL8AsECzlYuMkKiIK1hQAw24%2F9%2FSXEIDReAgf%2F%2FAQcqn3GbsCfwAAAAAElFTkSuQmCC)](https://lightsaml.slack.com/)
[![Slack invitations](https://img.shields.io/badge/slack%20invitations--green.svg?style=social&logo=data%3Aimage%2Fpng%3Bbase64%2CiVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAADAFBMVEUAAAA4Dzg%2BETo6M0o9amdKGUJFN09GUl0ck30dlH5lhh1xiBpoiCFpkTsimIEkmYcnm4cxqYk%2Fq4o0o5Y6so1AtY9AsJFAuZBAuZFFupNFr61qqoVvo5ZtuKp%2BvKhTwJxawqFgxaRpxqhqyKlhwcxuwshtyNlvyNpvytxxxMNwyduDEkyoHFu9FVvMDgzPDhrPGw%2FSFSbXEj3TNBPXLS3FE1zdE1ndFWHfFGPfFmTgGGTgHmrjK3LiMHXjNXnbXBXgeh3mZ3PqZJjraJrob57peILtd6WAjyS8nB2In06%2FtmXRoh3nnh3jjiPpqB7pqR%2FjqCTqrSnqtD%2FfvF7tt0bvu1fvvVPxxWvxyXTyy3mQoKCM1LyQ1b%2BM09%2BZ2cSV1diB0OGF0uKP1uSU1%2BWb2uap3OOq4Oqz4uyy4u301I8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAB6Dz29AAABAHRSTlP%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F%2F8AU%2FcHJQAAAAlwSFlzAAALEgAACxIB0t1%2B%2FAAAABh0RVh0U29mdHdhcmUAcGFpbnQubmV0IDQuMC45bDN%2BTgAAAMJJREFUKFNj%2BA8F4REQGiaQ6ecHYQAF4uRBjABvr9QkiICihDKQ4cnNq6qRCBaIERGLT1CX5uKRS4Go0OTjlNLQkHX3CAZpZUjTUuHnEIjN%2BB%2Fk5w8WSNZQExYUAbIiIfYw%2FE%2F%2F%2F19CIhrIdLA3MbdwA7tDQULpv5OtoZ6%2BmSXI0P%2F%2Fo1jYdC0sjAyMHcG2%2FLfTYWLUtrB29fUJBQvYWJiyMrMDJUL8AsECzlYuMkKiIK1hQAw24%2F9%2FSXEIDReAgf%2F%2FAQcqn3GbsCfwAAAAAElFTkSuQmCC)](https://lightsaml-slackin.herokuapp.com/)
[![Twitter](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/LightSamlPhp)


## SAML 2.0 SP Symfony bundle based on LightSAML.

[Getting Started](http://www.lightsaml.com/SP-Bundle/Getting-started/)

## Custom EntityId provider

To enable custom EntityId provider you have to write an implementation of `EntityIdProviderInterface`, next define it as service and configure it in `config.yml` with:
```
light_saml_sp:
    ...
    entity_id_provider: custom_entity_id_provider
    ...
```

## Using IDPData Metadata provider

To enable IDPData Metadata provider you have to add to `config.yml` what follows:
```
light_saml_sp:
    ...
    idp_data_metadata_provider:
        enabled: true
        idp_data_url: http://idp-data
        domain_resolver_url: http://domain-resolver
    ...
```

If you omit configuration above (or set `enabled: false`) then default metadata provider will be used.

## Logout

To enable single logout, add following entry to _security.yml_:

```
security:
    ...

    firewalls:
        main:
            light_saml_sp:
                ...
            logout:
                path: lightsaml_sp.logout
                target: default
                invalidate_session: false
                success_handler: security.firewall.logout_handler.lightsaml_sp
```

That's all. Now you can logout using path _/saml/logout_.

### Change-log

* __1.5.0_:
    - PPCDEV-6430 Sign LogoutRequests for SingleSignOn feature
* __1.4.1_:
    - PPCDEV-6258 Allow to enable IdpData with int value
* __1.4.0_:
    - PPCDEV-6208 Retrieve cert and private key from IdpData
    - PPCDEV-6154 use IdpData service for retrievieng metadata
* __1.3.0_:
    - Dynamic entity id
* __1.2.0_:
    - Generalized logout response
* __1.1.0_:
    - Added support for Single Logout

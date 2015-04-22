OhCache
=======

OhCache is a flexible caching library for PHP

[![Latest Stable Version](https://poser.pugx.org/rogerthomas84/ohcache/v/stable.svg)](https://packagist.org/packages/rogerthomas84/ohcache)
[![Total Downloads](https://poser.pugx.org/rogerthomas84/ohcache/downloads.svg)](https://packagist.org/packages/rogerthomas84/ohcache)
[![Latest Unstable Version](https://poser.pugx.org/rogerthomas84/ohcache/v/unstable.svg)](https://packagist.org/packages/rogerthomas84/ohcache)
[![License](https://poser.pugx.org/rogerthomas84/ohcache/license.svg)](https://packagist.org/packages/rogerthomas84/ohcache)
[![Build Status](https://travis-ci.org/rogerthomas84/ohcache.png)](http://travis-ci.org/rogerthomas84/ohcache)

Using Composer
--------------

To use OhCache with Composer, add the dependency (and version constraint) to your require block inside your `composer.json` file.

```json
{
    "require": {
        "rogerthomas84/ohcache": "1.1.*"
    }
}
```

Quick Start
-----------

OhCache exposes the following methods for Caching:

 * `APC`
 * `Memcache`
 * `FileSystem`

In addition to this, there is a `AdapterNull` which you can use for test (or development) purposes.

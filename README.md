OhCache
=======

OhCache is a flexible caching library for PHP

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
 * `Memcached`
 * `FileSystem`

In addition to this, there is a `AdapterNull` which you can use for test (or development) purposes.

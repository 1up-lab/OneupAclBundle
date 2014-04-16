OneupAclBundle
==============

The OneupAclBundle provides easy to use Acl features for your Symfony2 application. It is built on top of the Security component and comes with
handy features including:

* an [Acl manager](https://github.com/1up-lab/OneupAclBundle/blob/master/Resources/doc/manager.md), your entry point.
* [Doctrine listeners](https://github.com/1up-lab/OneupAclBundle/blob/master/Resources/doc/removal.md) for automatically remove Acl entries once an entity is deleted.
* [Check request parameters](https://github.com/1up-lab/OneupAclBundle/blob/master/Resources/doc/controller.md) against your access control lists.

[![Build Status](https://travis-ci.org/1up-lab/OneupAclBundle.png)](https://travis-ci.org/1up-lab/OneupAclBundle)
[![Total Downloads](https://poser.pugx.org/oneup/acl-bundle/downloads.png)](https://packagist.org/packages/oneup/acl-bundle)

Documentation
-------------

The entry point of the documentation can be found in the file `Resources/docs/index.md`

[Read the documentation for master](https://github.com/1up-lab/OneupAclBundle/blob/master/Resources/doc/index.md)

[Read the documentation for v0.10.1](https://github.com/1up-lab/OneupAclBundle/blob/v0.10.1/Resources/doc/index.md)

[Read the documentation for v0.9.1](https://github.com/1up-lab/OneupAclBundle/blob/v0.9.1/Resources/doc/index.md)

Upgrade Notes
-------------
* Added `oneup:acl:create` and `oneup:acl:delete` commands **v0.11.0** (Thanks to [jdeniau](https://github.com/jdeniau))
* Fixed bugs for [doctrine/mongodb-odm](https://github.com/doctrine/mongodb-odm) **v0.10.1**
* Changed default value of `remove_orphans` to false **v0.10.0**
* Fixed a bug in the DoctrineSubscriber **v0.9.1**
* First feature complete version **v0.9.0**

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/1up-lab/OneupAclBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.

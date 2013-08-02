# Use the Doctrine features

By default, only the DBAL layer of doctrine is needed to run this bundle. This is because Acl does not use the ORM but plain
queries for performance reasons. If you however choose to use Doctrine ORM, there are some more handy features this bundle
brings along.

## The RemoveListener

Inherently, Acl entries won't be deleted once the corresponding domain object is removed. The OneupAclBundle comes with a `RemoveListener`
which does this by default. So if you have the ORM installed, the acl entires will be automatically deleted.

You can still choose to opt-out of this behaviour in several ways.

### Turn off acl removal in configuration

Do so by setting the `remove_orphans` setting to `false`. In this case you fall back to Symfony`s original behaviour and nothing
gets deleted.

```yaml
# app/config/config.yml

oneup_acl:
    remove_orphans: false
```

### Turn off acl removal for specific entity

You can use the `DomainObject` annotation to opt-out of acl removal for a specific entity type.

```php
<?php

namespace Acme\DemoBundle\Entity;

use Oneup\AclBundle\Annotation as Acl;

/**
 * @Acl\DomainObject(remove=false)
 */
class Product
{
}
```

In this case Acl entries for the domain object `Product` will remain persistent in your database, even if the entity gets
deleted. Entries belonging to other domain objects will still be removed.
# Add class permissions

> By default, only the DBAL layer of doctrine is needed to run this bundle. This is because Acl does not use the ORM but plain
queries for performance reasons. If you however choose to use Doctrine ORM, there are some more handy features this bundle
brings along.

By using an Annotation you can define your class permissions directly on your entity.

```php

namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Oneup\AclBundle\Mapping\Annotation as Acl;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 * @Acl\DomainObject({
 *   @Acl\ClassPermission({ "ROLE_ADMIN" = MaskBuilder::MASK_IDDQD })
 * })
 */
class Post
{
}

```

This will listen to the `postPersist` event dispatched by Doctrine, and automatically add a new class permission if needed.
You can also add multiple permissions to one class.

```php
/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 * @Acl\DomainObject({
 *   @Acl\ClassPermission({ "ROLE_ADMIN" = MaskBuilder::MASK_IDDQD }),
 *   @Acl\ClassPermission({ "ROLE_USER" = MaskBuilder::MASK_VIEW })
 * })
 */
class Post
{
}

```

In addition, you can also add class field permissions by annotating a property correctly.

```php
/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 * @Acl\DomainObject()
 */
class Post
{
    /**
     * @Acl\PropertyPermission({ "ROLE_ADMIN" = MaskBuilder::MASK_EDIT })
     */
    protected $secured;
}

```

**Note**: Even if you just add property permissions, you still need the class annotation `Acl\DomainObject`.

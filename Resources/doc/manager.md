# Use the AclManager

One of the main purposes of this bundle is to provide a service which makes using Acl in your Symfony2 applications a breeze.
After activation of this bundle, there is a registered service named `oneup_acl.manager` in the service locator.

```php
$manager = $container->get('oneup_acl.manager');
```

The retrieved AclManager implements the [`AclManagerInterace`](https://github.com/1up-lab/OneupAclBundle/blob/master/Security/Acl/Model/AclManagerInterface.php) and therefore exposes several methods for you to use.

```php
<?php

namespace Oneup\AclBundle\Security\Acl\Model;

interface AclManagerInterface
{
    public function addObjectPermission($object, $identity, $mask);
    public function setObjectPermission($object, $identity, $mask);
    public function revokeObjectPermission($object, $identity, $mask);
    public function revokeObjectPermissions($object, $identity);
    public function revokeAllObjectPermissions($object);

    public function addObjectFieldPermission($object, $field, $identity, $mask);
    public function setObjectFieldPermission($object, $field, $identity, $mask);
    public function revokeObjectFieldPermission($object, $field, $identity, $mask);
    public function revokeObjectFieldPermissions($object, $field, $identity);
    public function revokeAllObjectFieldPermissions($object);

    public function addClassPermission($object, $identity, $mask);
    public function setClassPermission($object, $identity, $mask);
    public function revokeClassPermission($object, $identity, $mask);
    public function revokeClassPermissions($object, $identity);
    public function revokeAllClassPermissions($object);

    public function addClassFieldPermission($object, $field, $identity, $mask);
    public function setClassFieldPermission($object, $field, $identity, $mask);
    public function revokeClassFieldPermission($object, $field, $identity, $mask);
    public function revokeClassFieldPermissions($object, $field, $identity);
    public function revokeAllClassFieldPermissions($object);

    public function isGranted($attributes, $object = null);
}
```

## Check Permissions

If you want to perform a permission check use the `isGranted` method. Both parameters are directly piped into the `isGranted` method in the `security.context` service. More information on this is available in the [component docs](http://symfony.com/doc/current/components/security/firewall.html).

```php
// check if current logged in use has the role ROLE_ADMIN
$manager = $this->get('oneup_acl.manager');
$manager->isGranted('ROLE_ADMIN');
```

The previous example has nothing to do with Acl, so bring some more magic in!
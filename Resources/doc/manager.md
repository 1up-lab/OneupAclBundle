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
    public function addObjectPermission($object, $mask, $identity = null);
    public function setObjectPermission($object, $mask, $identity = null);
    public function revokeObjectPermission($object, $mask, $identity = null);
    public function revokeObjectPermissions($object, $identity = null);
    public function revokeAllObjectPermissions($object);

    public function addObjectFieldPermission($object, $field, $mask, $identity = null);
    public function setObjectFieldPermission($object, $field, $mask, $identity = null);
    public function revokeObjectFieldPermission($object, $field, $mask, $identity = null);
    public function revokeObjectFieldPermissions($object, $field, $identity = null);
    public function revokeAllObjectFieldPermissions($object);

    public function addClassPermission($object, $mask, $identity = null);
    public function setClassPermission($object, $mask, $identity = null);
    public function revokeClassPermission($object, $mask, $identity = null);
    public function revokeClassPermissions($object, $identity = null);
    public function revokeAllClassPermissions($object);

    public function addClassFieldPermission($object, $field, $mask, $identity = null);
    public function setClassFieldPermission($object, $field, $mask, $identity = null);
    public function revokeClassFieldPermission($object, $field, $mask, $identity = null);
    public function revokeClassFieldPermissions($object, $field, $identity = null);
    public function revokeAllClassFieldPermissions($object);

    public function isGranted($attributes, $object = null, $field = null);
}
```

## Check Permissions

If you want to perform a permission check use the `isGranted` method. Both parameters are directly piped into the `isGranted` method in the `security.context` service. More information on this is available in the [component docs](http://symfony.com/doc/current/components/security/firewall.html).

```php
// check if current logged in use has the role ROLE_ADMIN
$manager = $this->get('oneup_acl.manager');
$manager->isGranted('ROLE_ADMIN');
```

More interesting is the usage of Acl features. You can check permissions on domain objects like this:

```php
// retrieve a product from the database
$product = $repository->find(1);

// check if the current logged in user has the permission to view the product
$manager = $this->get('oneup_acl.manager');
$manager->isGranted('VIEW', $product);
```

You can provide a third parameter to the `isGranted` method, which will be used for field permission testing. So, to test
if the current loged in user has the permission to access the `name` property on `$product` use it like this:

```php
$manager->isGranted('VIEW', $product, 'name');
```

## Add and revoke permissions

The OneupAclBundle supports for types of permissions (object-, class-, object field- and class field permissions). All of them are represented with similar methods in the `AclManager`.

* `add*Permission`: Add a new permission entry. If no Acl for the given domain object exists, it will be created.
* `set*Permission`: Overwrite existing Acl entries with the given `SecurityIdentity` and mask.
* `revoke*Permission`: Revoke the Ace matching the triple "object", "identity" and "mask".
* `revoke*Permissions`: Revoke all entries for a given `SecurityIdentity` and object.
* `revokeAll*Permissions`: Revoke all entries associated with the given object.

## The parameters

* `object`: Your domain object to apply a permission on. It should implement a `getId` method which returns a unique id.
* `identity`: A `SecurityIdentity`. This is either a [Token](api.symfony.com/2.3/Symfony/Component/Security/Core/Authentication/Token.html), a [User](http://api.symfony.com/2.3/Symfony/Component/Security/Core/User/UserInterface.html), a [Role](http://api.symfony.com/2.3/Symfony/Component/Security/Core/Role/Role.html) or a string representing a Role. If you don't provide any value for this parameter, the current logged in user will be taken.
* `mask`: An integer representing a permission mask. You can use Symfony`s [MaskBuilder](http://api.symfony.com/2.3/Symfony/Component/Security/Acl/Permission/MaskBuilder.html) to create a mask.
* `field`: The name of a property to apply the permission to.

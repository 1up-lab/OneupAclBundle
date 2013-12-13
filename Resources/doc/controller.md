# Secure your controller arguments

> Note: If you are using the Sensio Framework Extra Bundle version 3.0+ you might prefer using the [@Security](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/security.html) annotation. The @Security annotation also supports the [ExpressionLanguage](http://symfony.com/doc/current/book/security.html#complex-access-controls-with-expressions) which might be more powerful.

Symfony2 can convert request parameters to objects and store it as request arguments. You can find more information about this topic in [the documentation](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html). However, this allows you to use a feature of the OneupAclBundle that checks these parameters against your acl rules.

To activate the automatic permission check, use the following annotation:

```php
<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Oneup\AclBundle\Configuration\ParamPermission;

class TestController extends Controller
{
    /**
     * @ParamPermission({ "product" = "VIEW" })
     */
    public function viewAction(Product $product)
    {
        // ...
    }

    /**
     * @ParamPermission({ "product" = "DELETE" })
     */
    public function deleteAction(Product $product)
    {
        // ...
    }
}
```

This way, before calling the acutal `view/deleteAction` method on the controller object, the parameter named `product` will be used for a acl permission check. An instance of `Symfony\Component\Security\Core\Exception\AccessDeniedException` will be thrown if the access was denied.

You can also secure more than one request parameter like this:

```php
/**
 * @ParamPermission({
 *   "one" = "VIEW",
 *   "two" = "VIEW"
 *  })
 */
public function anAction(Product $one, Product $two)
{
    // ...
}
```

or like this:

```php
/**
 * @ParamPermission({ "one" = "VIEW" })
 * @ParamPermission({ "two" = "VIEW" })
 */
public function anotherAction(Product $one, Product $two)
{
    // ...
}
```

**Note**: This feature depends on stuff in the `SensioFramworkExtraBundle` and is only available if the mentioned bundle is installed and activated.

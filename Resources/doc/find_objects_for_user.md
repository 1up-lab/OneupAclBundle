Access all objects for a specific user
===========================

For the moment, Symfony does not provide a good way to access all the registered objects for a user.\
They recommend to fetch all your objects, and test them one by one.

This can easily become a pain if you have a lot of items to load.

This bundle adds the reverse side to symfony: you can load all items for a user just with this command: 

```php
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

$aclProvider = $this->get('security.acl.provider');
$user = $this->get('security.context')->getToken()->getUser();

$class = 'Acme\DemoBundle\Entity\MyEntity';
$objectIdentities = $aclProvider->findObjectIdentitiesForUser($user, MaskBuilder::MASK_EDIT, $class);
foreach ($objectIdentities as $objectIdentity) {
    $id = $objectIdentity->getIdentifier(); // this is your database primary key
    $item = ... // fetch your object by id (doctrine, mongodb, propel, elasticsearch, etc.)
}
```

> Credit to: Phoenix Zerin and his [thread](http://stackoverflow.com/questions/20154865/find-aces-by-securityidentity-instead-of-objectidentity) on StackOverflow.

# IrfanTOOR\\Container

# Container
Container implementing Psr\\ContainerInterface, ArrayAccess, Countable and IteratorAggregate.

The identifiers can use dot notation to access an identifier down a
hierarchical level, e.g. to access ```$container['environment']['headers']['host']``` you
can code in doted notation as: ```$config['environment.headers.host']```

## Initializing

You can initialize by passing an array of key value pairs while creating a new instance, it will use array in memory for this case.

```php
<?php

use IrfanTOOR\Container;

$container = new IrfanTOOR\Container(
  # id => values
  'app' => [
    'name'    => 'My App'
    'version' => '1.1',
  ]
);
```

## Setting

You can by set a service in the Container by using the method 'set':

```php
use IrfanTOOR\Container;

$container = new IrfanTOOR\Container();

# setting hello => world
$container->set('hello', 'world');

# using an array notation
$container->set(['hello' => 'world']);

# defining multiple
$container->set([
  'hello'     => 'world',
  'box'       => 'empty',
  'something' => null,
  'array'     => [
    'action'       => 'go',
    'step'         => 1,
  ],
]);

# defining sub values
$container->set('app.name', 'Another App');
$container->set('debug.level', 2);

# defining a factory service
$container->factory('hash', function(){
    $salt = rand(0, 10000);
    return md5($salt . time());
});
```

using array access mechanism:

```php
$container['hello'] = 'world';
$container['debug.log.file'] = '/my/debug/log/file.log';
```

## Getting

You can get the stored value or the result from a service in the container by
its identifier using the method 'get':

```php
# returns a random hash
$hash = $container->get('hash');
```
you can also use the array access:

```php
$hash = $container['hash'];
```

## Checking if a value is present in the container

You can use the method 'has' to check if the container has an entry identified
with the identifier id:

```php
if ($container->has('hash')) {
  # this will be executed even if the given identifier has the value NULL, 0
  # or false
  echo 'random hash : ' . $container->get('hash');
}
```

using the array access the above code will become:

```php
if (isset($container['hash']) {
  # this will be executed even if the given identifier has the value NULL, 0
  # or false
  echo 'random hash : ' . $container['hash'];
}
```

## Removing an entry

You can use the method 'remove' or unset on the element:

```php
# using method remove
$container->remove('hash');

# using unset
unset($container['hash']);
```

## Container to Array

The method 'toArray' can be used to convert the container into an array:

```php
$array = $container->toArray();
```

## Array of service identifiers
The array of services identifiers can be retrieved by using the method 'keys':

```php
$services = $container->keys();
```

## Count

The number of items present in the container can be retrieved using the method
'count'. Note that it will return the count of the items at base level.

```php
# will return 1 for the Collection defined in initialization section
$count = $container->count();
```

## Iteration

The container can directly be used in a foreach loop or wherever an iterator
is used. for example the code:

```php
foreach($container->toArray() as $k => $v)
{
    $v->close();
}
```

can be simplified as:

```php
foreach($container as $k => $v)
{
    $v->close();
}
```

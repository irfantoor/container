<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use IrfanTOOR\Container;
use IrfanTOOR\Debug;

Debug::enable(1);

$c = new Container();

$c->set('onetime', function() {
    return md5(time() . rand(1, 10000));
});

$c->factory('hash', function() {
    return md5(time() . rand(1, 10000));
});

for ($i=3; $i>=0; $i--) {
    echo
        "onetime: " . $c->get('onetime') .
        ", hash: " . $c->get('hash') .
        PHP_EOL;
}

dd($c);

<?php
require __DIR__ . "/../vendor/autoload.php";

use PhpSh\Condition;
use PhpSh\Script;

$lessThan10 = Condition::create('$i')->lessThan(10);
$script = new Script();

echo $script
    ->while($lessThan10, function (Script $script) {
        $script->printf('$i\n');
        $script->increment('i');
    })
    ->generate();

<?php

namespace Spatie\Skeleton\Tests;

use PhpSh\Condition;
use PhpSh\Script;
use PHPUnit\Framework\TestCase;

class ConditionTests extends TestCase
{
    /** @test */
    public function it_can_build_an_if_statement()
    {
        $script = new Script();
        $condition = Condition::create('$i')->lessThan(10);
        $sh = $script
            ->set('i', 5)
            ->if($condition, function (Script $script) {
                $script->printf('OK');
            })
            ->endif()
            ->generate();
        $this->assertEquals('OK', shell_exec($sh));
    }
}

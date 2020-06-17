<?php


namespace PhpSh\Tests;

use PhpSh\Condition;
use PhpSh\Script;
use PHPUnit\Framework\TestCase;

class ScriptingLoopsTest extends TestCase
{
    /** @test */
    public function it_can_create_while_loops()
    {
        $condition = Condition::create('$i')->lessThan(10);

        $sh = (new Script())
            ->set('i', 0)
            ->while($condition, function (Script $script) {
                $script->echo('$i');
                $script->increment('i');
            })
            ->generate();
        $this->assertEquals('0123456789', shell_exec($sh));
    }

    /** @test */
    public function it_can_control_loop_flow()
    {
        $condition = Condition::create('$i')->lessThan(10);

        $sh = (new Script())
            ->set('i', 0)
            ->while($condition, function (Script $script) {
                $script->increment('i');
                $script
                    ->if(Condition::create('$i')->lessThan(5), function (Script $script) {
                        $script->continue();
                    })
                    ->endif();
                $script
                    ->if(Condition::create('$i')->greaterThan(7), function (Script $script) {
                        $script->break();
                    })
                    ->endif();
                $script->echo('$i');
            })
            ->generate();
        $this->assertEquals('567', shell_exec($sh));
    }
}

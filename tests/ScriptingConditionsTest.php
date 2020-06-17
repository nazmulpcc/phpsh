<?php

namespace PhpSh\Tests;

use PhpSh\Condition;
use PhpSh\Script;
use PHPUnit\Framework\TestCase;

class ScriptingConditionsTest extends TestCase
{
    /** @test */
    public function it_can_build_an_if_statement()
    {
        $condition = Condition::create();
        $sh = (new Script())
            ->set('i', 10)
            ->if($condition->is('i')->equals(10), function (Script $script) {
                $script->printf('OK');
            })
            ->endif()
            ->generate();
        $this->assertEquals('OK', shell_exec($sh));
    }

    /** @test */
    public function it_can_build_an_if_statement_with_multiple_conditions()
    {
        $sh = (new Script())
            ->set('i', 5)
            ->if(Condition::create('$i')->lessThan(10)->and()->is('i')->greaterThan('1'), function (Script $script) {
                $script->printf("OK");
            })
            ->endif()
            ->generate();
        $this->assertEquals('OK', shell_exec($sh));
    }

    /** @test */
    public function it_can_build_an_if_else_statement()
    {
        $sh = (new Script())
            ->set('i', 15)
            ->if(Condition::create('$i')->lessThan(10), function (Script $script) {
                $script->printf("NOT_OK");
            })->else(function (Script $script) {
                $script->printf("OK");
            })
            ->generate();
        $this->assertEquals('OK', shell_exec($sh));
    }

    /** @test */
    public function it_can_build_an_if_elseif_else_statement()
    {
        $sh = (new Script())
            ->set('i', 25)
            ->if(Condition::create('$i')->lessThan(10), function (Script $script) {
                $script->printf('NOT_OK');
            })->elseif(Condition::create('$i')->greaterThan(20), function (Script $script) {
                $script->printf('OK');
            })->else(function (Script $script) {
                $script->printf('NOT_OK');
            })
            ->generate();
        $this->assertEquals('OK', shell_exec($sh));
    }

    /** @test */
    public function it_can_build_switch_statement()
    {
        $sh = (new Script())
            ->set('i', 1)
            ->decrement('i')
            ->switch('i', function (Script $script) {
                $script
                    ->case('1', function (Script $script) {
                        $script->echo('NOT_OK');
                    })
                    ->case('*', function (Script $script) {
                        $script->echo('OK');
                    });
            });

        $this->assertEquals('OK', shell_exec($sh));
    }
}

<?php


namespace PhpSh\Tests;

use PhpSh\Condition;
use PHPUnit\Framework\TestCase;

class ConditionsBuildingTest extends TestCase
{
    /** @test */
    public function it_can_create_comparison_operator_conditions()
    {
        $this->assertEquals('$i -eq 10', Condition::create('$i')->equals(10)->generate());
        $this->assertEquals('$i -ne 10', Condition::create('$i')->notEquals(10)->generate());
        $this->assertEquals('$i -lt 10', Condition::create('$i')->lessThan(10)->generate());
        $this->assertEquals('$i -gt 10', Condition::create('$i')->greaterThan(10)->generate());
        $this->assertEquals('$i -ge 10', Condition::create('$i')->notLessThan(10)->generate());
        $this->assertEquals('$i -le 10', Condition::create('$i')->notGreaterThan(10)->generate());
    }

    /** @test */
    public function it_can_create_logical_operator_conditions()
    {
        $this->assertEquals(
            '$i -lt 10 -a $i -gt 1',
            Condition::create('$i')
                ->lessThan(10)
                ->and()
                ->is('$i')
                ->greaterThan(1)
                ->generate()
        );
        $this->assertEquals(
            '$i -lt 10 -o $i -gt 1',
            Condition::create('$i')
                ->lessThan(10)
                ->or()
                ->is('$i')
                ->greaterThan(1)
                ->generate()
        );
    }

    /** @test */
    public function it_can_create_variable_checks()
    {
        $this->assertEquals('-z ${i+x}', Condition::create()->isset('i')->generate());
        $this->assertEquals('-z $i', Condition::create()->isEmpty('i')->generate());
        $this->assertEquals('-n $i', Condition::create()->isNotEmpty('i')->generate());
    }

    /** @test */
    public function it_can_create_file_and_directory_checks()
    {
        $this->assertEquals('-f path', Condition::create()->isFile('path')->generate());
        $this->assertEquals('-x path', Condition::create()->executable('path')->generate());
        $this->assertEquals('-w path', Condition::create()->writable('path')->generate());
        $this->assertEquals('-r path', Condition::create()->readable('path')->generate());
        $this->assertEquals('-s path', Condition::create()->notEmptyFile('path')->generate());
        $this->assertEquals('-e path', Condition::create()->pathExists('path')->generate());
        $this->assertEquals('-d path', Condition::create()->directoryExists('path')->generate());
    }
}

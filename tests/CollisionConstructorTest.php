<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use Time\Day;
use Time\Exceptions\InvalidDateTimeException;

class CollisionConstructorTest extends TestCase
{
    /** @test */
    public function constructorFull()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $this->assertCount(60, $object->minutes()->range()); // 60 minutos
    }

    /** @test */
    public function constructorDate()
    {
        $object = new Collision('2020-11-01', '2020-11-01');
        $this->assertCount(60*24, $object->minutes()->range()); // 1440 minutos
    }

    /** @test */
    public function constructorSyntaxException()
    {
        $this->expectException(InvalidDateTimeException::class);
        new Collision('2020-11', '2020:00');
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The end date must be greater than the start date of the period');
        new Collision('2020-11-02', '2020-11-01');
    }
}

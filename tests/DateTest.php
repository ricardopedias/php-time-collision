<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Date;
use Time\Exceptions\InvalidDateException;


class DateTest extends TestCase
{
    /** @test */
    public function constructor()
    {
        $object = new Date('2020-11-01');
        $this->assertEquals(new DateTime('2020-11-01 00:00:00'), $object->day());
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidDateException::class);

        new Date('00000');
    }
}

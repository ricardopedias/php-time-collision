<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;
use Time\Exceptions\InvalidDateTimeException;

class CollisionfittingsBetweenTest extends TestCase
{
    /** @test */
    public function fittingsBetween()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:20', '12:30')
               ->allowDefaultPeriod('12:35', '12:40');

        $this->assertEquals([
                0 => [ new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00') ],
                1 => [ new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00') ]
            ], 
            $object->fittingsBetween('2020-11-01 12:00:00', '2020-11-01 13:00:00')
        );
    }

    /** @test */
    public function exceptionBefore()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The specified date and time is out of range');

        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:20', '12:30')
               ->allowDefaultPeriod('12:35', '12:40');

        $object->fittingsBetween('2020-11-01 11:00:00', '2020-11-01 12:00:00');
    }

    /** @test */
    public function exceptionAfter()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The specified date and time is out of range');

        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:20', '12:30')
               ->allowDefaultPeriod('12:35', '12:40');

        $object->fittingsBetween('2020-11-01 14:00:00', '2020-11-01 15:00:00');
    }
}
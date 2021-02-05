<?php

declare(strict_types=1);

namespace Tests\Ranges;

use TimeCollision\Collision;
use DateTime;
use Tests\TestCase;
use TimeCollision\Days\Interval;
use TimeCollision\Exceptions\InvalidDateTimeException;

class FillsFittingsBetweenTest extends TestCase
{
    /** @test */
    public function fittingsBetween()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->fromDefaults()
            ->enablePeriod('12:20', '12:30')
            ->enablePeriod('12:35', '12:40');

        $this->assertEquals([
                0 => new Interval('2020-11-01 12:20:00', '2020-11-01 12:30:00'),
                1 => new Interval('2020-11-01 12:35:00', '2020-11-01 12:40:00')
            ], 
            $object->fromFillings()->getFittingsBetween('2020-11-01 12:00:00', '2020-11-01 13:00:00')
        );
    }

    /** @test */
    public function exceptionBefore()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The specified date and time is out of range');

        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->fromDefaults()
            ->enablePeriod('12:20', '12:30')
            ->enablePeriod('12:35', '12:40');

        $object->fromFillings()->getFittingsBetween('2020-11-01 11:00:00', '2020-11-01 12:00:00');
    }

    /** @test */
    public function exceptionAfter()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The specified date and time is out of range');

        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->fromDefaults()
            ->enablePeriod('12:20', '12:30')
            ->enablePeriod('12:35', '12:40');

        $object->fromFillings()->getFittingsBetween('2020-11-01 14:00:00', '2020-11-01 15:00:00');
    }
}
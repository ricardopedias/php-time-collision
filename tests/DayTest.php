<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use Time\WeekDay;
use Time\Exceptions\InvalidDayException;
use Time\Exceptions\InvalidTimeException;

class DayTest extends TestCase
{
    /** @test */
    public function constructor()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $this->assertEquals(WeekDay::MONDAY, $object->day());
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidDayException::class);
        $this->expectExceptionMessage('The day must be 0 to 7, or use Week::???');
        
        new WeekDay(8);
    }

    /** @test */
    public function withPeriodSyntaxException()
    {
        $this->expectException(InvalidTimeException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('00:00', '00,00');
    }

    /** @test */
    public function withPeriodException()
    {
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('The end time must be greater than the start time of the period');
        
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('09:00', '08:00');
    }
}

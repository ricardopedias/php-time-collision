<?php

declare(strict_types=1);

namespace Tests\Days;

use Tests\TestCase;
use TimeCollision\Days\WeekDay;
use TimeCollision\Exceptions\InvalidWeekDayException;

class WeekDayTest extends TestCase
{
    /** @test */
    public function constants()
    {
        $this->assertEquals(0, WeekDay::SUNDAY);
        $this->assertEquals(1, WeekDay::MONDAY);
        $this->assertEquals(2, WeekDay::TUESDAY);
        $this->assertEquals(3, WeekDay::WEDNESDAY);
        $this->assertEquals(4, WeekDay::THURSDAY);
        $this->assertEquals(5, WeekDay::FRIDAY);
        $this->assertEquals(6, WeekDay::SATURDAY);
        $this->assertEquals(7, WeekDay::ALL_DAYS);
    }

    /** @test */
    public function constructor()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $this->assertEquals(WeekDay::MONDAY, $object->getDay());
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidWeekDayException::class);
        $this->expectExceptionMessage('The day must be 0 to 7, or use Week::???');
        
        new WeekDay(8);
    }
}
<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use SplFixedArray;
use Time\Collision;
use Time\WeekDay;
use Time\Exceptions\InvalidWeekDayException;

class CollisionAllowDaysTest extends TestCase
{
    /** @test */
    public function defaultAllDays()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertEquals(new SplFixedArray(0), $object->minutes()->allowed());
    }

    /** @test */
    public function allowOneDayDefaultPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDayOfWeek(WeekDay::MONDAY);

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function allowOneDayWithPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDayOfWeek(WeekDay::MONDAY)
            ->withPeriod('08:00', '09:00');

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function allowDateDefaultPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDayOfWeek(WeekDay::MONDAY);
        $object->allowDate('2020-11-03');

        // das 8 as 9 do segundo dia: Segunda-feira
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end1 = $start1 + 60;

        // das 8 as 8:30 do terceiro dia: Terça-feira
        $start2 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end2 = $start2 + 30;

        $result = $this->makeRange("{$start1}..{$end1}", "{$start2}..{$end2}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function allowDayException()
    {
        $this->expectException(InvalidWeekDayException::class);
        $this->expectExceptionMessage('The day must be 0 to 7, or use Week::???');
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDayOfWeek(8);
    }
}

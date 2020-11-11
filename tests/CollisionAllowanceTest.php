<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\WeekDay;
use Time\Exceptions\InvalidDateTimeException;
use Time\Exceptions\InvalidDayException;
use Time\Exceptions\InvalidTimeException;
use Time\Minutes;

class CollisionAllowanceTest extends TestCase
{
    /** @test */
    public function defaultAllDaysDefaultPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');

        // das 8 as 9 do primeiro dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result += $this->period("{$start}..{$end}", 0);

        // das 8 as 8:30 do terceiro dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00'));
        $end = $start + 30;
        $result += $this->period("{$start}..{$end}", 0);
        
        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function defaultAllDaysWithoutPeriods()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertEquals([], $object->allowed());
    }

    /** @test */
    public function allowOneDayDefaultPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(WeekDay::MONDAY);

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function allowOneDayWithPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowDay(WeekDay::MONDAY)
            ->withPeriod('08:00', '09:00');

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function allowOnePeriod()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:15', '12:31');

        $result = $this->period('1..14', Minutes::UNUSED)
            + $this->period('15..31', Minutes::ALLOWED)
            + $this->period('32..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $object->range());
    }

    /** @test */
    public function allowTwoPeriods()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:10', '12:25');
        $object->allowPeriod('12:35', '12:50');

        $result = $this->period('1..9', Minutes::UNUSED)
            + $this->period('10..25', Minutes::ALLOWED)
            + $this->period('26..34', Minutes::UNUSED)
            + $this->period('35..50', Minutes::ALLOWED)
            + $this->period('51..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $object->range());
    }

    /** @test */
    public function allowThreePeriods()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:10', '12:20');
        $object->allowPeriod('12:30', '12:40');
        $object->allowPeriod('12:50', '13:00');

        $result = $this->period('1..9', Minutes::UNUSED)
            + $this->period('10..20', Minutes::ALLOWED)
            + $this->period('21..29', Minutes::UNUSED)
            + $this->period('30..40', Minutes::ALLOWED)
            + $this->period('41..49', Minutes::UNUSED)
            + $this->period('50..60', Minutes::ALLOWED);
        
        $this->assertEquals($result, $object->range());
    }

    /** @test */
    public function allowDateDefaultPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(WeekDay::MONDAY);
        $object->allowDate('2020-11-03');

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        // das 8 as 8:30 do terceiro dia: Terça-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00'));
        $end = $start + 30;
        $result += $this->period("{$start}..{$end}", 0);

        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function allowDayException()
    {
        $this->expectException(InvalidDayException::class);
        $this->expectExceptionMessage('The day must be 0 to 7, or use Week::???');
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDay(8);
    }

    /** @test */
    public function allowPeriodSyntaxException()
    {
        $this->expectException(InvalidTimeException::class);
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('00:00', '00,00');
    }

    /** @test */
    public function allowPeriodException()
    {
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('The end time must be greater than the start time of the period');
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('09:00', '08:00');
    }

    /** @test */
    public function allowDateSyntaxException()
    {
        $this->expectException(InvalidDateTimeException::class);
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDate('2020:01');
    }
}

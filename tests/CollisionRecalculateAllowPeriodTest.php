<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\WeekDay;
use Time\Minutes;

class CollisionRecalculateAllowPeriodTest extends TestCase
{
    /** @test */
    public function periodToAllowAllDays()
    {
        // PERIOD
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

        // + ALL DAYS
        $object->allowAllDays();

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
    public function periodToDay()
    {
        // PERIOD
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

        // + DAY
        $object->allowDay(WeekDay::MONDAY);

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function periodToPeriod()
    {
        // PERIOD
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

        // + PERIOD
        $object->allowPeriod('10:00', '11:00');

        // das 8 as 9 do primeiro dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        // das 10 as 11 do primeiro dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 10:00:00'));
        $end = $start + 60;
        $result += $this->period("{$start}..{$end}", 0);

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result += $this->period("{$start}..{$end}", 0);

        // das 10 as 11 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 10:00:00'));
        $end = $start + 60;
        $result += $this->period("{$start}..{$end}", 0);

        // das 8 as 8:30 do terceiro dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00'));
        $end = $start + 30;
        $result += $this->period("{$start}..{$end}", 0);
        
        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function periodToDate()
    {
        // PERIOD
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

        // + DATE
        $object->allowDate('2020-11-03');

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00'));
        $end = $start + 30;
        $result = $this->period("{$start}..{$end}", 0);

        $this->assertEquals($result, $object->allowed());
    }
}

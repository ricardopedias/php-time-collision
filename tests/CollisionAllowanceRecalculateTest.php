<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\Day;
use Time\Minutes;

// defaultAllDays
// allowDay
// allowPeriod
// allowDate

class CollisionAllowanceRecalculateTest extends TestCase
{
    /** @test */
    public function defaultDaysToOneDay()
    {
        // Primeiro cálculo
        // Libera todos os dias
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

        // Setar um dia força o recálculo de minutos
        // Libera apenas a Segunda-feira
        $object->allowDay(Day::MONDAY);

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);
        
        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function oneDayToTwoDays()
    {
        // Primeiro cálculo
        // Libera apenas a Segunda-feira
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(Day::MONDAY);

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);
        $this->assertEquals($result, $object->allowed());

        // Liberar a Terça-feira força o realculo
        $object->allowDay(Day::TUESDAY);

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
    public function oneDayToDate()
    {
         // Primeiro cálculo
        // Libera apenas a Segunda-feira
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(Day::MONDAY);

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);
        $this->assertEquals($result, $object->allowed());

        // Liberar a Terça-feira força o realculo
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
    public function oneDayOnePeriodToOneDayTwoPeriods()
    {
         // Primeiro cálculo
        // Libera apenas a Segunda-feira
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(Day::MONDAY);

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);
        $this->assertEquals($result, $object->allowed());

        // Liberar a Terça-feira força o realculo
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
    public function oneDefaultPeriodToTwoDefaultPeriods()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:00', '12:30');

        $result = $this->period('1..30', Minutes::ALLOWED)
            + $this->period('31..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $object->range());

        $object->allowPeriod('12:40', '12:50');

        $result = $this->period('1..30', Minutes::ALLOWED)
            + $this->period('31..39', Minutes::UNUSED)
            + $this->period('40..50', Minutes::ALLOWED)
            + $this->period('51..60', Minutes::UNUSED);

        $this->assertEquals($result, $object->range());
    }

    
}

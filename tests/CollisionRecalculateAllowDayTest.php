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

class CollisionRecalculateAllowDayTest extends TestCase
{
    /** @test */
    public function dayToAllowAllDays()
    {
        // Primeiro cálculo
        // Libera somente a Segunda-feira
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(Day::MONDAY);

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);
        
        $this->assertEquals($result, $object->allowed());

        // Setar todos os dias
        // Libera tudo
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
    public function dayToPeriod()
    {
        // Primeiro cálculo
        // Libera somente a Segunda-feira
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowPeriod('08:00', '09:00');
        $object->allowDay(Day::MONDAY);

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);
        $this->assertEquals($result, $object->allowed());

        // Setar um periodo força o recálculo de minutos
        // Libera apenas a Segunda-feira mas em dois periodos
        $object->allowPeriod('10:00', '11:00');

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00'));
        $end = $start + 60;
        $result = $this->period("{$start}..{$end}", 0);

        // das 10 as 11 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 10:00:00'));
        $end = $start + 60;
        $result += $this->period("{$start}..{$end}", 0);

        $this->assertEquals($result, $object->allowed());
    }

    /** @test */
    public function dayToDate()
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

        // Setar uma data força o recálculo de minutos
        // Libera apenas a Terça-feira
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
}

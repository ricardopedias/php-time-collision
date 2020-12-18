<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\Exceptions\InvalidDateException;
use Time\WeekDay;

class CollisionAllowDatesTest extends TestCase
{
    /** @test */
    public function allowDateDefaultPeriod()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDay(WeekDay::MONDAY);
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
    public function allowDateSyntaxException()
    {
        $this->expectException(InvalidDateException::class);
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDate('2020:01');
    }
}

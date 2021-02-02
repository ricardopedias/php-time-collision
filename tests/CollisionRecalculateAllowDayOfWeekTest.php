<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\WeekDay;

class CollisionRecalculateAllowDayOfWeekTest extends TestCase
{
    /** @test */
    public function specificDayToAllDays()
    {
        // DAY
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDayOfWeek(WeekDay::MONDAY); // 02/11/2020

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->allowed());

        $object->allowAllWeekDays();

        // das 8 as 9 do primeiro dia
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 08:00:00')) - 1;
        $end1 = $start1 + 60;

        // das 8 as 9 do segundo dia
        $start2 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end2 = $start2 + 60;

        // das 8 as 8:30 do terceiro dia
        $start3 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end3 = $start3 + 30;

        $result = $this->makeRange(
            "{$start1}..{$end1}",
            "{$start2}..{$end2}",
            "{$start3}..{$end3}"
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function specificDayToSpecificDay()
    {
        // DAY
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDayOfWeek(WeekDay::MONDAY); // 02/11/2020

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());

        // + DAY
        $object->allowDayOfWeek(WeekDay::TUESDAY); // 03/11/2020

        // das 8 as 9 do segundo dia: Segunda-feira
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end1 = $start1 + 60;

        // das 8 as 8:30 do terceiro dia: Terça-feira
        $start2 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end2 = $start2 + 30;

        $result = $this->makeRange(
            "{$start1}..{$end1}",
            "{$start2}..{$end2}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function specificDayToAddedDefaultPeriod()
    {
        // DAY
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDayOfWeek(WeekDay::MONDAY); // 02/11/2020

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());

        // + PERIOD
        $object->allowDefaultPeriod('10:00', '11:00');

        // das 8 as 9 do segundo dia
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end1 = $start1 + 60;

        // das 10 as 11 do segundo dia
        $start2 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 10:00:00')) - 1;
        $end2 = $start2 + 60;

        $result = $this->makeRange(
            "{$start1}..{$end1}",
            "{$start2}..{$end2}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function specificDayToSpecificDate()
    {
        // DAY
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
        $object->allowDayOfWeek(WeekDay::MONDAY); // 02/11/2020

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());

        // + DATE
        $object->allowDate('2020-11-03'); // Terça-feira

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
}

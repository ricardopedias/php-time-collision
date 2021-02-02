<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\Minutes;
use Time\WeekDay;

class CollisionRecalculateAllowPeriodTest extends TestCase
{
    /** @test */
    public function defaultPeriodToAllowAllDays()
    {
        // PERIOD
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');

        $result = $this->makeRange('0..0');
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));

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
            "{$start3}..{$end3}",
        );
        
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));

        // das 8 as 9 do primeiro dia
        $start4 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 08:00:00')) - 1;
        $end4 = $start4 + 60;

        // das 8 as 9 do segundo dia
        $start5 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end5 = $start5 + 60;

        // das 8 as 8:30 do terceiro dia
        $start6 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end6 = $start6 + 30;

        $result = $this->makeRange(
            "{$start4}..{$end4}", 
            "{$start5}..{$end5}", 
            "{$start6}..{$end6}", 
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
    }

    /** @test */
    public function defaultPeriodToSpecificDay()
    {
        // PERIOD
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');

        $result = $this->makeRange('0..0');
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
        
        // Libera somente a segunda-feira
        $object->allowDayOfWeek(WeekDay::MONDAY); // 02/11/2020

        // das 8 as 9 do segundo dia
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end1 = $start1 + 60;

        $result = $this->makeRange(
            "{$start1}..{$end1}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));

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
            "{$start3}..{$end3}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
    }

    /** @test */
    public function defaultPeriodToAddedDefaultPeriod()
    {
        // PERIOD
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowDefaultPeriod('08:00', '09:00');

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
            "{$start3}..{$end3}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));

        // adiciona o período das 10 às 11
        $object->allowDefaultPeriod('10:00', '11:00');

        // das 8 as 9 do primeiro dia
        $start4 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 08:00:00')) - 1;
        $end4 = $start4 + 60;

        // das 10 as 11 do primeiro dia
        $start5 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 10:00:00')) - 1;
        $end5 = $start5 + 60;

        // das 8 as 9 do segundo dia
        $start6 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end6 = $start6 + 60;

        // das 10 as 11 do segundo dia
        $start7 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 10:00:00')) - 1;
        $end7 = $start7 + 60;

        // das 8 as 8:30 do terceiro dia
        $start8 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end8 = $start8 + 30;

        $result = $this->makeRange(
            "{$start4}..{$end4}",
            "{$start5}..{$end5}",
            "{$start6}..{$end6}",
            "{$start7}..{$end7}",
            "{$start8}..{$end8}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
    }

    /** @test */
    public function defaultPeriodToSpecificDate()
    {
        // PERIOD
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->disableAllWeekDays();
        $object->allowDefaultPeriod('08:00', '09:00');
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
            "{$start3}..{$end3}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));

        // + DATE
        $object->disableAllWeekDays();
        $object->allowDate('2020-11-03');

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end = $start + 30;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
    }
}

<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\WeekDay;

class CollisionRecalculateDefaultAllDaysTest extends TestCase
{
    /** @test */
    public function defaultAllDaysToAllDays()
    {
        // DEFAULT
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
        
        $this->assertEquals($result, $object->minutes()->allowed());

        $object->allowAllDays();

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
        
        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function defaultAllDaysToSpecificDay()
    {
        // DEFAULT
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
        
        $this->assertEquals($result, $object->minutes()->allowed());

        // libera apenas a segunda-feira
        $object->disableAllDays();
        $object->allowDay(WeekDay::MONDAY); // 02/11/2020

        // das 8 as 9 do segundo dia
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function defaultAllDaysToAddedDefaultPeriod()
    {
        // DEFAULT
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

        $this->assertEquals($result, $object->minutes()->allowed());

        // + PERIOD
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
        
        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function defaultAllDaysToSpecificDate()
    {
        // DEFAULT
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

        // por padrão, todos os dias do range estáo liberados
        $result = $this->makeRange(
            "{$start1}..{$end1}",
            "{$start2}..{$end2}",
            "{$start3}..{$end3}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->allowed());

        // determina um dia específico
        $object->disableAllDays();
        $object->allowDate('2020-11-03'); // Terça-feira

        // das 8 as 9 do segundo dia
        $start4 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end4 = $start4 + 30;

        // somente o dia determinado conta 
        $result = $this->makeRange(
            "{$start4}..{$end4}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function defaultAllDaysToSpecificDayPlusDate()
    {
        // DEFAULT
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowDefaultPeriod('08:00', '09:00');

        // das 8 as 9 do primeiro dia - Domingo
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-01 08:00:00')) - 1;
        $end1 = $start1 + 60;

        // das 8 as 9 do segundo dia - Segunda-feira
        $start2 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end2 = $start2 + 60;

        // das 8 as 8:30 do terceiro dia - Terça-feira
        $start3 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end3 = $start3 + 30;

        // por padrão, todos os dias do range estáo liberados
        $result = $this->makeRange(
            "{$start1}..{$end1}",
            "{$start2}..{$end2}",
            "{$start3}..{$end3}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->allowed());

        // Libera as Segundas-feiras
        $object->disableAllDays();
        $object->allowDay(WeekDay::MONDAY); // 02/11/2020

        // Somente a Segunda-feira é contada
        $result = $this->makeRange(
            "{$start1}..{$end1}",
        );

        // libera um dia específico - Terça-feira
        $object->allowDate('2020-11-03');

        // das 8 as 9 do segundo dia - Segunda-feira
        $start4 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end4 = $start4 + 60;

        // das 8 as 8h30 do terceiro dia - Terça-feira
        $start5 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end5 = $start5 + 30;

        // somente o dia determinado conta 
        $result = $this->makeRange(
            "{$start4}..{$end4}",
            "{$start5}..{$end5}",
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->allowed());
    }
}

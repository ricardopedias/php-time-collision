<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use SplFixedArray;
use Time\Collision;
use Time\Exceptions\InvalidTimeException;
use Time\Minutes;

class CollisionAllowPeriodsTest extends TestCase
{
    /** @test */
    public function defaultAllDaysDefaultPeriod()
    {
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
            "{$start3}..{$end3}"
        );
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->allowed());
    }

    /** @test */
    public function allowOnePeriod()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:15', '12:31');

        $resultAll = $this->period('0..13', Minutes::UNUSED)
            + $this->period('14..30', Minutes::ALLOWED)
            + $this->period('31..59', Minutes::UNUSED);
        $resultAll = SplFixedArray::fromArray($resultAll);

        $resultUnused = $this->makeRange('0..13', '31..59');
        $resultUnused = $this->rangeToDatetime('2020-11-01 12:00:00', $resultUnused);

        $resultAllowed = $this->makeRange('14..30');
        $resultAllowed = $this->rangeToDatetime('2020-11-01 12:00:00', $resultAllowed);
        
        $this->assertEquals($resultAll, $object->minutes()->range());
        $this->assertEquals($resultUnused, $object->minutes()->unused());
        $this->assertEquals($resultAllowed, $object->minutes()->allowed());
    }

    /** @test */
    public function allowTwoPeriods()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:10', '12:25');
        $object->allowDefaultPeriod('12:35', '12:50');

        $result = $this->period('0..8', Minutes::UNUSED)
            + $this->period('9..24', Minutes::ALLOWED)
            + $this->period('25..33', Minutes::UNUSED)
            + $this->period('34..49', Minutes::ALLOWED)
            + $this->period('50..59', Minutes::UNUSED);
        $result = SplFixedArray::fromArray($result);

        $this->assertEquals($result, $object->minutes()->range());
    }

    /** @test */
    public function allowThreePeriods()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:10', '12:20');
        $object->allowDefaultPeriod('12:30', '12:40');
        $object->allowDefaultPeriod('12:50', '13:00');

        $result = $this->period('0..8', Minutes::UNUSED)
            + $this->period('9..19', Minutes::ALLOWED)
            + $this->period('20..28', Minutes::UNUSED)
            + $this->period('29..39', Minutes::ALLOWED)
            + $this->period('40..48', Minutes::UNUSED)
            + $this->period('49..59', Minutes::ALLOWED);
        $result = SplFixedArray::fromArray($result);

        $this->assertEquals($result, $object->minutes()->range());
    }

    /** @test */
    public function allowDefaultPeriodSyntaxException()
    {
        $this->expectException(InvalidTimeException::class);
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('00:00', '00,00');
    }

    /** @test */
    public function allowDefaultPeriodException()
    {
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('The end time must be greater than the start time of the period');
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('09:00', '08:00');
    }
}

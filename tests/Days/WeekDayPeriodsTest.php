<?php

declare(strict_types=1);

namespace Tests\Days;

use Tests\TestCase;
use TimeCollision\Days\Period;
use TimeCollision\Days\WeekDay;
use TimeCollision\Exceptions\InvalidPeriodException;
use TimeCollision\Exceptions\InvalidTimeException;

class WeekDayPeriodsTest extends TestCase
{
    /** @test */
    public function withPeriod()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('10:00', '11:00');
        $object->withPeriod('14:00', '15:00');

        $this->assertEquals([
            new Period("10:00", "11:00"),
            new Period("14:00", "15:00")
        ], $object->getPeriods());
    }

    /** @test */
    public function withPeriodSyntaxFirstArgumentException()
    {
        $this->expectException(InvalidTimeException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('---', '14:00');
    }

    /** @test */
    public function withPeriodSyntaxSecondArgumentException()
    {
        $this->expectException(InvalidTimeException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('12:00', '-----');
    }

    /** @test */
    public function withPeriodFutureException()
    {
        $this->expectException(InvalidPeriodException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('14:00', '12:00'); // não é possível voltar para o passado
    }

    /** @test */
    public function withPeriodsArray()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriodsArray([
            [ "12:00", "14:00"],
            [ "15:00", "16:00"]
        ]);

        $this->assertEquals([
            new Period("12:00", "14:00"),
            new Period("15:00", "16:00")
        ], $object->getPeriods());
    }

    /** @test */
    public function withPeriodsArrayException()
    {
        $this->expectException(InvalidPeriodException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriodsArray([
            ["14:00"],
        ]);
    }

    /** @test */
    public function getPeriodsArray()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriodsArray([
            [ "12:00", "14:00"],
            [ "15:00", "16:00"]
        ]);

        $this->assertEquals([
            ["12:00", "14:00"],
            ["15:00", "16:00"]
        ], $object->getPeriodsArray());
    }
}

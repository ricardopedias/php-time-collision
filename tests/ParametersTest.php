<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Exceptions\InvalidDateException;
use Time\Exceptions\InvalidDateTimeException;
use Time\Exceptions\InvalidTimeException;
use Time\Exceptions\InvalidWeekDayException;
use Time\Parameters;
use Time\WeekDay;

class ParametersTest extends TestCase
{
    /** @test */
    public function defaults()
    {
        $bag = new Parameters();

        $this->assertCount(7, $bag->getWeekDays());
        $this->assertEquals([0,1,2,3,4,5,6], array_keys($bag->getWeekDays()));
        for ($x = 0; $x <= 6; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getWeekDays()[$x]);
        }
    }

    /** @test */
    public function setWeekDay()
    {
        $bag = new Parameters();
        $bag->setWeekDay(WeekDay::MONDAY);

        $this->assertCount(7, $bag->getWeekDays());
        $this->assertEquals([0,1,2,3,4,5,6], array_keys($bag->getWeekDays()));
        for ($x = 0; $x <= 6; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getWeekDays()[$x]);
        }
    }

    /** @test */
    public function setWeekDayException()
    {
        $this->expectException(InvalidWeekDayException::class);

        $bag = new Parameters();
        $bag->setWeekDay(99);
    }

    /** @test */
    public function unsetWeekDay()
    {
        $bag = new Parameters();
        $bag->unsetWeekDay(WeekDay::MONDAY);

        $this->assertCount(6, $bag->getWeekDays());
        $this->assertEquals([0, 2, 3, 4, 5, 6], array_keys($bag->getWeekDays()));
        for ($x = 0; $x <= 6 && $x !== 1; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getWeekDays()[$x]);
        }
    }

    /** @test */
    public function unsetWeekDayException()
    {
        $this->expectException(InvalidWeekDayException::class);

        $bag = new Parameters();
        $bag->unsetWeekDay(99);
    }

    /** @test */
    public function unsetAllWeekDays()
    {
        $bag = new Parameters();
        $bag->unsetAllWeekDays();

        $this->assertCount(0, $bag->getWeekDays());
    }

    /** @test */
    public function setAllWeekDays()
    {
        $bag = new Parameters();
        $bag->unsetAllWeekDays();
        $this->assertCount(0, $bag->getWeekDays());

        $bag->setAllWeekDays();

        $this->assertCount(7, $bag->getWeekDays());
        $this->assertEquals([0,1,2,3,4,5,6], array_keys($bag->getWeekDays()));
        for ($x = 0; $x <= 6; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getWeekDays()[$x]);
        }
    }

    /** @test */
    public function setDate()
    {
        $bag = new Parameters();
        $bag->setDate('2020-10-01');
        $bag->setDate('2020-10-02');

        $this->assertCount(2, $bag->getDates());
        $this->assertArrayHasKey('2020-10-01', $bag->getDates());
        $this->assertArrayHasKey('2020-10-02', $bag->getDates());
    }

    /** @test */
    public function setDateException()
    {
        $this->expectException(InvalidDateException::class);

        $bag = new Parameters();
        $bag->setDate('0000000');
    }

    /** @test */
    public function unsetDate()
    {
        $bag = new Parameters();
        $bag->setDate('2020-10-01');
        $bag->setDate('2020-10-02');
        $this->assertCount(2, $bag->getDates());
        $this->assertArrayHasKey('2020-10-01', $bag->getDates());
        $this->assertArrayHasKey('2020-10-02', $bag->getDates());
        $this->assertCount(0, $bag->getDisabledDates());

        $bag->unsetDate('2020-10-01');

        $this->assertCount(1, $bag->getDates());
        $this->assertArrayHasKey('2020-10-02', $bag->getDates());

        $this->assertCount(1, $bag->getDisabledDates());
        $this->assertArrayHasKey('2020-10-01', $bag->getDisabledDates());
    }

    /** @test */
    public function unsetDateException()
    {
        $this->expectException(InvalidDateException::class);

        $bag = new Parameters();
        $bag->unsetDate('0000000');
    }

    /** @test */
    public function setDefaultPeriod()
    {
        $bag = new Parameters();
        $bag->setDefaultPeriod('14:30', '18:45');
        $bag->setDefaultPeriod('19:00', '20:10');

        $this->assertCount(2, $bag->getDefaultPeriods());
        $this->assertEquals(['14:30', '18:45'], $bag->getDefaultPeriods()[0]);
        $this->assertEquals(['19:00', '20:10'], $bag->getDefaultPeriods()[1]);
    }

    /** @test */
    public function setDefaultPeriodSyntaxException()
    {
        $this->expectException(InvalidTimeException::class);

        $bag = new Parameters();
        $bag->setDefaultPeriod('00:00', '00,00');
    }

    /** @test */
    public function setDefaultPeriodException()
    {
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('The end time must be greater than the start time of the period');
        
        $bag = new Parameters();
        $bag->setDefaultPeriod('09:00', '08:00');
    }

    /** @test */
    public function setFilled()
    {
        $bag = new Parameters();
        $bag->setFilled('2020-11-15 10:00', '2020-11-16 11:00');
        $bag->setFilled('2020-11-15 19:00', '2020-11-16 20:00');

        $this->assertCount(2, $bag->getFills());
        $this->assertCount(0, $bag->getCumulativeFills());
        $this->assertEquals([ new DateTime('2020-11-15 10:00'), new DateTime('2020-11-16 11:00') ], $bag->getFills()[0]);
        $this->assertEquals([ new DateTime('2020-11-15 19:00'), new DateTime('2020-11-16 20:00') ], $bag->getFills()[1]);
    }

    /** @test */
    public function setCumulativeFilled()
    {
        $bag = new Parameters();
        $bag->setFilled('2020-11-15 10:00', '2020-11-16 11:00', true);
        $bag->setFilled('2020-11-15 19:00', '2020-11-16 20:00', true);

        $this->assertCount(0, $bag->getFills());
        $this->assertCount(2, $bag->getCumulativeFills());
        $this->assertEquals([ new DateTime('2020-11-15 10:00'), new DateTime('2020-11-16 11:00') ], $bag->getCumulativeFills()[0]);
        $this->assertEquals([ new DateTime('2020-11-15 19:00'), new DateTime('2020-11-16 20:00') ], $bag->getCumulativeFills()[1]);
    }

    /** @test */
    public function setBothFilleds()
    {
        $bag = new Parameters();
        $bag->setFilled('2020-11-15 10:00', '2020-11-16 11:00', false);
        $bag->setFilled('2020-11-15 19:00', '2020-11-16 20:00', true);

        $this->assertCount(1, $bag->getFills());
        $this->assertCount(1, $bag->getCumulativeFills());
        $this->assertEquals([ new DateTime('2020-11-15 10:00'), new DateTime('2020-11-16 11:00') ], $bag->getFills()[0]);
        $this->assertEquals([ new DateTime('2020-11-15 19:00'), new DateTime('2020-11-16 20:00') ], $bag->getCumulativeFills()[0]);
    }

    /** @test */
    public function setFilledSyntaxExceptionStart()
    {
        $this->expectException(InvalidDateTimeException::class);

        $bag = new Parameters();
        $bag->setFilled('000000 10,00', '2020-11-16 11:00');
    }

    /** @test */
    public function setFilledSyntaxExceptionEnd()
    {
        $this->expectException(InvalidDateTimeException::class);

        $bag = new Parameters();
        $bag->setFilled('2020-11-15 10:00', '000000 11,00');
    }

    /** @test */
    public function setFilledException()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The end datetime must be greater than the start datetime of the period');

        $bag = new Parameters();
        $bag->setFilled('2020-11-15 10:00', '2020-11-14 11:00');
    }
}

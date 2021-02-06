<?php

declare(strict_types=1);

namespace Tests\Days;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Days\Week;
use TimeCollision\Days\WeekDay;
use TimeCollision\Days\Year;
use TimeCollision\Days\YearDay;
use TimeCollision\Exceptions\InvalidYearDayException;
use TimeCollision\Exceptions\InvalidWeekDayException;

class DaysDisableTest extends TestCase
{
    /** @test */
    public function disableWeekDay()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->disableDay(WeekDay::MONDAY);

        $this->assertCount(6, $bag->getAllDays());
        $this->assertEquals([0, 2, 3, 4, 5, 6], array_keys($bag->getAllDays()));
        for ($x = 0; $x <= 6 && $x !== 1; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getAllDays()[$x]);
        }
    }

    /** @test */
    public function disableWeekDayException()
    {
        $this->expectException(InvalidWeekDayException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->disableDay(99);
    }

    /** @test */
    public function disableAllWeekDays()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->disableAllDays();

        $this->assertCount(0, $bag->getAllDays());
    }

    /** @test */
    public function fullDisabledWeekDays()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->disableAllDays();

        $this->assertCount(0, $bag->getAllDays());
        $this->assertCount(7, $bag->getAllDisabledDays());
        $this->assertEquals([
            0 => new WeekDay(0), 
            1 => new WeekDay(1),
            2 => new WeekDay(2),
            3 => new WeekDay(3),
            4 => new WeekDay(4),
            5 => new WeekDay(5),
            6 => new WeekDay(6)
        ], $bag->getAllDisabledDays());
    }

    /** @test */
    public function allDisabledWeekDays()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->disableAllDays();

        $bag->enableDay(WeekDay::MONDAY);

        $this->assertCount(1, $bag->getAllDays());
        $this->assertCount(6, $bag->getAllDisabledDays());
        $this->assertEquals([
            0 => new WeekDay(0), 
            2 => new WeekDay(2),
            3 => new WeekDay(3),
            4 => new WeekDay(4),
            5 => new WeekDay(5),
            6 => new WeekDay(6)
        ], $bag->getAllDisabledDays());
    }

    /** @test */
    public function disableYearDay()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Year($collision);
        $bag->enableDay('2020-10-01');
        $bag->enableDay('2020-10-02');
        $this->assertCount(2, $bag->getAllDays());
        $this->assertCount(0, $bag->getAllDisabledDays());
        
        $this->assertArrayHasKey('2020-10-01', $bag->getAllDays());
        $this->assertArrayHasKey('2020-10-02', $bag->getAllDays());

        $bag->disableDay('2020-10-01');

        $this->assertCount(1, $bag->getAllDays());
        $this->assertCount(1, $bag->getAllDisabledDays());

        $this->assertArrayHasKey('2020-10-02', $bag->getAllDays());
        $this->assertEquals([
            '2020-10-01' => new YearDay('2020-10-01')
        ], $bag->getAllDisabledDays());
    }

    /** @test */
    public function disableYearDayAndEnable()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Year($collision);
        $bag->enableDay('2020-10-01');
        $bag->enableDay('2020-10-02');

        $this->assertCount(2, $bag->getAllDays());
        $this->assertCount(0, $bag->getAllDisabledDays());
        $this->assertArrayHasKey('2020-10-01', $bag->getAllDays());
        $this->assertArrayHasKey('2020-10-02', $bag->getAllDays());

        $bag->disableDay('2020-10-02');

        $this->assertCount(1, $bag->getAllDays());
        $this->assertCount(1, $bag->getAllDisabledDays());
        $this->assertArrayHasKey('2020-10-01', $bag->getAllDays());
        $this->assertArrayHasKey('2020-10-02', $bag->getAllDisabledDays());

        $bag->enableDay('2020-10-02');

        $this->assertCount(2, $bag->getAllDays());
        $this->assertCount(0, $bag->getAllDisabledDays());
        $this->assertArrayHasKey('2020-10-01', $bag->getAllDays());
        $this->assertArrayHasKey('2020-10-02', $bag->getAllDays());
    }

    /** @test */
    public function disableYearDayException()
    {
        $this->expectException(InvalidYearDayException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Year($collision);
        $bag->disableDay('0000000');
    }
}

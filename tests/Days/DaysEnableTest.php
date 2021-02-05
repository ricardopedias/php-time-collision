<?php

declare(strict_types=1);

namespace Tests\Days;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Days\Week;
use TimeCollision\Days\WeekDay;
use TimeCollision\Days\Year;
use TimeCollision\Exceptions\InvalidWeekDayException;
use TimeCollision\Exceptions\InvalidYearDayException;

class DaysEnabledTest extends TestCase
{
    /** @test */
    public function defaults()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);

        $this->assertCount(7, $bag->getAllDays());
        $this->assertEquals([0,1,2,3,4,5,6], array_keys($bag->getAllDays()));
        for ($x = 0; $x <= 6; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getAllDays()[$x]);
        }
    }

    /** @test */
    public function enableWeekDay()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->enableDay(WeekDay::MONDAY);

        $this->assertCount(7, $bag->getAllDays());
        $this->assertEquals([0,1,2,3,4,5,6], array_keys($bag->getAllDays()));
        for ($x = 0; $x <= 6; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getAllDays()[$x]);
        }
    }

    /** @test */
    public function enableWeekDayException()
    {
        $this->expectException(InvalidWeekDayException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);

        $bag->enableDay(99);
    }

    /** @test */
    public function enableAllWeekDays()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Week($collision);
        $bag->disableAllDays();
        $this->assertCount(0, $bag->getAllDays());

        $bag->enableAllDays();

        $this->assertCount(7, $bag->getAllDays());
        $this->assertEquals([0,1,2,3,4,5,6], array_keys($bag->getAllDays()));
        for ($x = 0; $x <= 6; $x++) {
            $this->assertInstanceOf(WeekDay::class, $bag->getAllDays()[$x]);
        }
    }

    /** @test */
    public function enableYearDay()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Year($collision);
        $bag->enableDay('2020-10-01');
        $bag->enableDay('2020-10-02');

        $this->assertCount(2, $bag->getAllDays());
        $this->assertArrayHasKey('2020-10-01', $bag->getAllDays());
        $this->assertArrayHasKey('2020-10-02', $bag->getAllDays());
    }

    /** @test */
    public function enableYearDayException()
    {
        $this->expectException(InvalidYearDayException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Year($collision);
        $bag->enableDay('0000000');
    }
}

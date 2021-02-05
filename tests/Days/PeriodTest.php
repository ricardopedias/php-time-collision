<?php

declare(strict_types=1);

namespace Tests\Days;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Days\Period;
use TimeCollision\Days\Time;
use TimeCollision\Days\Week;
use TimeCollision\Days\WeekDay;
use TimeCollision\Days\Year;
use TimeCollision\Days\YearDay;
use TimeCollision\Exceptions\InvalidPeriodException;
use TimeCollision\Exceptions\InvalidTimeException;
use TimeCollision\Exceptions\InvalidYearDayException;
use TimeCollision\Exceptions\InvalidWeekDayException;

class PeriodTest extends TestCase
{
    /** @test */
    public function constructionFutureException()
    {
        $this->expectException(InvalidPeriodException::class);

        new Period('01:00', '00:00');
    }

    /** @test */
    public function constructionSintaxFirstException()
    {
        $this->expectException(InvalidTimeException::class);

        new Period('----', '00:00');
    }

    /** @test */
    public function constructionSintaxSecondException()
    {
        $this->expectException(InvalidTimeException::class);

        new Period('01:00', '----');
    }

    /** @test */
    public function periodInfo()
    {
        $period = new Period('01:05', '02:00');
        $this->assertInstanceOf(Time::class, $period->getStart());
        $this->assertEquals('01:05', $period->getStart()->toString());
        $this->assertEquals(1, $period->getStart()->getHour());
        $this->assertEquals(5, $period->getStart()->getMinute());
    }
}

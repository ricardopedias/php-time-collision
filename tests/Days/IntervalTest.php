<?php

declare(strict_types=1);

namespace Tests\Days;

use DateTime;
use Tests\TestCase;
use TimeCollision\Days\Interval;
use TimeCollision\Exceptions\InvalidDateTimeException;

class IntervalTest extends TestCase
{
    /** @test */
    public function constructionFutureException()
    {
        $this->expectException(InvalidDateTimeException::class);

        new Interval('1980-01-10 10:00', '1980-01-10 09:00');
    }

    /** @test */
    public function constructionSintaxFirstException()
    {
        $this->expectException(InvalidDateTimeException::class);

        new Interval('----', '1980-01-10 09:00');
    }

    /** @test */
    public function constructionSintaxSecondException()
    {
        $this->expectException(InvalidDateTimeException::class);

        new Interval('1980-01-10 10:00', '----');
    }

    /** @test */
    public function periodInfo()
    {
        $interval = new Interval('1980-01-10 10:05', '1980-01-10 11:00');
        $this->assertInstanceOf(DateTime::class, $interval->getStart());
        $this->assertInstanceOf(DateTime::class, $interval->getEnd());
    }
}

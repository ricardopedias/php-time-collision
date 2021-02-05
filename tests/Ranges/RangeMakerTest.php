<?php

declare(strict_types=1);

namespace Tests\Ranges;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class RangeMakerTest extends TestCase
{
    /** @test */
    public function makeMinutesRangeInstance()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 12:05:00');

        $maker = new RangeMaker($collision);
        $this->assertInstanceOf(Minutes::class, $maker->makeMinutesRange());
    }

    /** @test */
    public function withoutDefaultPeriods()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([], $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function withDefaultPeriods()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00');
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([4,5,6,7,8,9], $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function withDefaultPeriodsDisableAllWeekDays()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([], $minutes->getRange(Minutes::ALLOWED)->toArray());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Ranges;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Days\WeekDay;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class RangeMakerWeekDaysPeriodsTest extends TestCase
{
    /** @test */
    public function useGlobalDefaultPeriod()
    {
        // Domingo
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00'); 
        $collision->fromDefaults()
            ->enablePeriod('00:00', '00:05'); // seta o período padrão

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(0, 4), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function overwriteGlobalDefaultPeriod()
    {
        // Domingo
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00'); 
        $collision->fromDefaults()
            ->enablePeriod('00:00', '00:05'); // seta o período padrão

        $collision->fromWeek()->enableDay(WeekDay::SUNDAY)
            ->withPeriod('00:10', '00:15'); // isso sobrescreve o período padrão

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(9, 14), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function useWeekDayPeriod()
    {
        // Domingo
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00'); 

        $collision->fromWeek()->enableDay(WeekDay::SUNDAY)
            ->withPeriod('00:10', '00:15'); // isso será usado como período padrão

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(9, 14), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }
}

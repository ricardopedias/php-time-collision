<?php

declare(strict_types=1);

namespace Tests\Ranges;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Days\WeekDay;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class RangeMakerWeekDaysTest extends TestCase
{
    /** @test */
    public function applyWeekDaysInSunday()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa os Domingos
        $collision->fromWeek()->enableDay(WeekDay::SUNDAY);

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(4, 9), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyWeekDaysInMonday()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa as Segundas
        $collision->fromWeek()->enableDay(WeekDay::MONDAY);

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(1444, 1449), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyWeekDaysInBoth()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa Domingo e Segunda
        $collision->fromWeek()->enableDay(WeekDay::SUNDAY);
        $collision->fromWeek()->enableDay(WeekDay::MONDAY);

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(
            array_merge(range(4, 9), range(1444, 1449)), 
            $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyWeekDaysWithoutDefaultPeriods()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa os domingos
        $collision->fromWeek()->enableDay(WeekDay::SUNDAY);

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([], $minutes->getRange(Minutes::ALLOWED)->toArray());
    }
}

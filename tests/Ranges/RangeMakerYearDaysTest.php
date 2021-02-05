<?php

declare(strict_types=1);

namespace Tests\Ranges;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class RangeMakerYearDaysTest extends TestCase
{
    /** @test */
    public function applyYearDaysInSunday()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa somente a Domingo dia 1
        $collision->fromYear()->enableDay('2020-11-01');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(4, 9), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyYearDaysInMonday()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa somente a Segunda dia 2
        $collision->fromYear()->enableDay('2020-11-02');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(1444, 1449), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyYearDaysInBoth()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa Domingo e Segunda
        $collision->fromYear()->enableDay('2020-11-01');
        $collision->fromYear()->enableDay('2020-11-02');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(array_merge(range(4, 9), range(1444, 1449)), 
            $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyYearDaysWithDefaultPeriods()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 

        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa somente a Domingo dia 1
        $collision->fromYear()->enableDay('2020-11-01');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(4, 9), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyYearDaysWithoutDefaultPeriods()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // ativa somente a Domingo dia 1
        $collision->fromYear()->enableDay('2020-11-01');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([], $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function applyYearDaysWithPeriod()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        // habilitar o dia específico tb habilita o dia da semana
        $collision->fromYear()->enableDay('2020-11-01')
            ->withPeriod('00:05', '00:10');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(4, 9), $minutes->getRange(Minutes::ALLOWED)->toArray());
    }
}

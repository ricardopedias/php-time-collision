<?php

declare(strict_types=1);

namespace Tests\Ranges;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Days\WeekDay;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class RangeMakerYearDaysDisableTest extends TestCase
{
    /** @test */
    public function disableFirstYearDay()
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

        $collision->fromYear()->disableDay('2020-11-01');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(1444, 1449), 
            $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function disableSecondYearDay()
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

        $collision->fromYear()->disableDay('2020-11-02');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(range(4, 9), 
            $minutes->getRange(Minutes::ALLOWED)->toArray());
    }

    /** @test */
    public function disableFirstWeekDay()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        $collision->fromYear()->enableDay('2020-11-01'); // Domingo <-- habilitado explicitamente
        $collision->fromYear()->enableDay('2020-11-02'); // Segunda 

        // Não é possível desativar o dia 2020-11-01 (Domingo) que tenha 
        // sido habilitado explicitamente, mesmo que ele seja um Domingo
        $collision->fromWeek()->disableDay(WeekDay::SUNDAY);

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(
            array_merge(range(4, 9), range(1444, 1449)), 
            $minutes->getRange(Minutes::ALLOWED)->toArray()
        );
    }

    /** @test */
    public function disableSecondWeekDay()
    {
        $collision = new Collision(
            '2020-11-01 00:00:00', // Domingo [PHP 0]
            '2020-11-02 00:15:00'  // Segunda [PHP 1]
        ); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        // desativa todos os dias da semana
        $collision->fromWeek()->disableAllDays();

        $collision->fromYear()->enableDay('2020-11-01'); // Domingo
        $collision->fromYear()->enableDay('2020-11-02'); // Segunda <-- habilitada explicitamente

        // Não é possível desativar o dia 2020-11-02 (Segunda) que tenha 
        // sido habilitado explicitamente, mesmo que ele seja uma Segunda-feira
        $collision->fromWeek()->disableDay(WeekDay::MONDAY);

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals(
            array_merge(range(4, 9), range(1444, 1449)), 
            $minutes->getRange(Minutes::ALLOWED)->toArray()
        );
    }
}

<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;
use Time\Minutes;

class MinutesRangeInfoTest extends TestCase
{
    /** @test */
    public function unused()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'), Minutes::ALLOWED);

        $result = $this->period('1..14', Minutes::UNUSED) 
            + $this->period('32..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $minutes->range(Minutes::UNUSED));
    }

    /** @test */
    public function allowed()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'), Minutes::ALLOWED);

        $result = $this->period('15..31', Minutes::ALLOWED);
        
        $this->assertEquals($result, $minutes->range(Minutes::ALLOWED));
    }

    /** @test */
    public function filled()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00'), Minutes::ALLOWED);
        $minutes->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00'), Minutes::ALLOWED);

        $minutes->mark(new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00'), Minutes::FILLED);

        // periodo 1: insere do 25 ao 30... 
        // ignora o restante até 34 - porque não faz parte dos ranges liberados
        $result = $this->period('25..30', Minutes::FILLED); 
        $this->assertEquals($result, $minutes->range(Minutes::FILLED));
    }

    /** @test */
    public function filledCumulative()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00'), Minutes::ALLOWED);
        $minutes->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00'), Minutes::ALLOWED);

        $minutes->markCumulative(new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00'), Minutes::FILLED);

        // periodo 1: insere do 25 ao 30 - o que cabe
        // ignora o que não faz parte dos ranges liberados e guarda os minutos de 30 a 34 = 4 minutos
        // periodo 2: insere os 4 minutos guardados = 35 ao 39
        $result = $this->period('25..30', Minutes::FILLED) + $this->period('35..39', Minutes::FILLED); 
        $this->assertEquals($result, $minutes->range(Minutes::FILLED));
    }
}
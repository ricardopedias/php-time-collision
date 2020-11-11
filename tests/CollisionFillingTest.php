<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\Minutes;

class CollisionFillingTest extends TestCase
{
    /** @test */
    public function fill()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:20', '12:30')
               ->allowPeriod('12:35', '12:40');

        $object->fill('2020-11-01 12:25:00', '2020-11-01 12:34:00');

        // periodo 1: insere do 25 ao 30... 
        // ignora o restante até 34 - porque não faz parte dos ranges liberados
        $result = $this->period('25..30', Minutes::FILLED); 
        $this->assertEquals($result, $object->minutes()->filled());
    }

    /** @test */
    public function fillCumulative()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:20', '12:30')
               ->allowPeriod('12:35', '12:40');

        // Precisa de 10 minutos (contando o minuto 25)
        $object->fill('2020-11-01 12:25:00', '2020-11-01 12:34:00', true);

        $result = $this->period('25..30', Minutes::FILLED) // + 6 minutos (contando o 25)
            + $this->period('35..38', Minutes::FILLED); // + 4 minutos (contando o 35)
        $this->assertEquals($result, $object->minutes()->filled());
    }
}

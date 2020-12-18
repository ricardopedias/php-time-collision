<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;

class CollisionFillingTest extends TestCase
{
    /** @test */
    public function fill()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:20', '12:30')
               ->allowDefaultPeriod('12:35', '12:40');

        $object->fill('2020-11-01 12:25:00', '2020-11-01 12:34:00');

        // periodo 1: insere do 25 ao 30... 
        // ignora o restante até 34 - porque não faz parte dos ranges liberados
        $result = $this->makeRange('24..29'); 
        $result = $this->rangeToDatetime('2020-11-01 12:00:00', $result);

        $this->assertEquals($result, $object->minutes()->filled());
    }

    /** @test */
    public function fillCumulative()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:20', '12:30')
               ->allowDefaultPeriod('12:35', '12:40');

        // Precisa de 10 minutos (contando o minuto 25)
        $object->fillCumulative('2020-11-01 12:25:00', '2020-11-01 12:34:00');

        $result = $this->makeRange('24..29', '34..37'); // + 4 minutos (contando o 35)
        $result = $this->rangeToDatetime('2020-11-01 12:00:00', $result);
        
        $this->assertEquals($result, $object->minutes()->filled());
    }
}

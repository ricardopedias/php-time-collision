<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;

class CollisionFittingsForTest extends TestCase
{
    /** @test */
    public function fittingsFor()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowDefaultPeriod('12:20', '12:30')
               ->allowDefaultPeriod('12:35', '12:40');

        $this->assertEquals([
            19 => [ new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00') ]
        ], $object->fittingsFor(10));

        $this->assertEquals([
            19 => [ new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00') ],
            34 => [ new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00') ],
        ], $object->fittingsFor(5));

        $this->assertEquals([
            // array vazio
        ], $object->fittingsFor(15));
    }
}
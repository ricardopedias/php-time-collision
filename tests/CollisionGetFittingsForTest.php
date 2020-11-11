<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;
use Time\Minutes;

class CollisionGetFittingsForTest extends TestCase
{
    /** @test */
    public function getFittingsFor()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->allowPeriod('12:20', '12:30')
               ->allowPeriod('12:35', '12:40');

        $this->assertEquals([
            20 => [ new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00') ]
        ], $object->getFittingsFor(10));

        $this->assertEquals([
            20 => [ new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00') ],
            35 => [ new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00') ],
        ], $object->getFittingsFor(5));

        $this->assertEquals([
            // array vazio
        ], $object->getFittingsFor(15));
    }
}
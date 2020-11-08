<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;
use Time\Minutes;

class CollisionGetFittingsForTest extends TestCase
{
    /** @test */
    public function getFittings()
    {
        $object = new Collision(new Minutes($this->dateStart, $this->dateEnd));
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        $this->assertEquals([
            20 => [ 20, 30 ]
        ], $object->getFittingsFor(10));

        $this->assertEquals([
            20 => [ 20, 30 ],
            35 => [ 35, 40 ],
        ], $object->getFittingsFor(5));

        $this->assertEquals([
            // array vazio
        ], $object->getFittingsFor(15));
    }
}
<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Chunks;
use Time\Minutes;

class ChunksFittingsTest extends TestCase
{
    /** @test */
    public function getFittings()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00'), Minutes::ALLOWED); // periodo 1
        $minutes->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00'), Minutes::ALLOWED); // periodo 2

        $object = new Chunks($minutes);

        $this->assertEquals([
            20 => [ 20, 30 ]
        ], $object->fittings(10));

        $this->assertEquals([
            20 => [ 20, 30 ],
            35 => [ 35, 40 ],
        ], $object->fittings(5));

        $this->assertEquals([
            // array vazio
        ], $object->fittings(15));
    }
}
<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Chunks;
use Time\Minutes;

class ChunksDaysTest extends TestCase
{
    /** @test */
    public function fullDays()
    {
        $rangeObject = new Minutes(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-04 23:59:59'));
        $object = new Chunks($rangeObject);

        $this->assertCount(5759, $rangeObject->range());
        $this->assertCount(4, $object->days());

        $this->assertArrayHasKey(1, $object->days());
        $this->assertArrayHasKey(1440, $object->days());
        $this->assertArrayHasKey(1440*2, $object->days());
        $this->assertArrayHasKey(1440*3, $object->days());
    }

    /** @test */
    public function partialDays()
    {
        $rangeObject = new Minutes(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-04 08:00:00'));
        $object = new Chunks($rangeObject);

        $this->assertCount(4080, $rangeObject->range());
        $this->assertCount(4, $object->days());

        $this->assertArrayHasKey(1, $object->days());
        $this->assertArrayHasKey(720, $object->days()); // 12 horas
        $this->assertArrayHasKey(720 + 1440, $object->days()); // 12 horas + 1 dia
        $this->assertArrayHasKey(720 + 1440*2, $object->days()); // 12 horas + 2 dias
    }
}
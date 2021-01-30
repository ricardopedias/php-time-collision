<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Chunks;
use Time\Exceptions\InvalidDateTimeException;
use Time\Minutes;

class ChunksFillablesTest extends TestCase
{
    /** @test */
    public function fillablesOutOfRange()
    {
        $this->expectException(InvalidDateTimeException::class);
        
        $range_1200_1300 = $this->makeRangeObject();
        $chunksObject = new Chunks($range_1200_1300);
        $chunksObject->fillables('2020-11-01 11:00', '2020-11-01 11:05');
    }

    protected function makeRangeObject(string $start = '2020-11-01 12:00:00', string $end = '2020-11-01 13:00:00'): Minutes
    {
        $rangeObject = new Minutes(new DateTime($start), new DateTime($end));

        // Libera no inicio do range
        $rangeObject->mark(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 12:25:00'), Minutes::ALLOWED);
        $rangeObject->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:50:00'), Minutes::ALLOWED);

        return $rangeObject;
    }
}
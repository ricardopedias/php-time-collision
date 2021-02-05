<?php

declare(strict_types=1);

namespace Tests\Ranges;

use DateTime;
use SplFixedArray;
use Tests\TestCase;
use TimeCollision\Ranges\Minutes;

class MinutesRangeInfoTest extends TestCase
{
    /** @test */
    public function indexes()
    {
        $dateStart = new DateTime('2020-11-01 12:00:00');
        $dateEnd   = new DateTime('2020-11-01 13:00:00');
        $minutes = new Minutes($dateStart, $dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'), Minutes::ALLOWED);

        $this->assertCount(60, $minutes->getRange());
        $this->assertInstanceOf(SplFixedArray::class, $minutes->getRange());
        $this->assertArrayHasKey(0, $minutes->getRange());
        $this->assertArrayHasKey(59, $minutes->getRange());
        $this->assertArrayNotHasKey(60, $minutes->getRange());
    }

    /** @test */
    public function unused()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'), Minutes::ALLOWED);

        $result = $this->makeRange('0..13', '31..59');
        
        $this->assertInstanceOf(SplFixedArray::class, $minutes->getRange(Minutes::UNUSED));
        $this->assertEquals($result, $minutes->getRange(Minutes::UNUSED));
    }

    /** @test */
    public function allowed()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'), Minutes::ALLOWED);

        $result = $this->makeRange('14..30');
        
        $this->assertInstanceOf(SplFixedArray::class, $minutes->getRange(Minutes::ALLOWED));
        $this->assertEquals($result, $minutes->getRange(Minutes::ALLOWED));
    }

    /** @test */
    public function filled()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00'), Minutes::ALLOWED);
        $minutes->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00'), Minutes::ALLOWED);

        $minutes->mark(new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00'), Minutes::FILLED);

        // periodo 1: insere do minuto 25 ao 30... 
        // ignora o restante até 34 - porque não faz parte dos ranges liberados
        $result = $this->makeRange('24..29');

        $this->assertInstanceOf(SplFixedArray::class, $minutes->getRange(Minutes::FILLED));
        $this->assertEquals($result, $minutes->getRange(Minutes::FILLED));
    }

    /** @test */
    public function filledCumulative()
    {
        $minutes = new Minutes($this->dateStart, $this->dateEnd);
        $minutes->mark(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00'), Minutes::ALLOWED);
        $minutes->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00'), Minutes::ALLOWED);

        // Precisa de 10 minutos (contando o minuto 25)
        $minutes->markCumulative(new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00'), Minutes::FILLED);

        $result = $this->makeRange(
            '24..29', // + 6 minutos (contando o 25)
            '34..37' // + 4 minutos (contando o 35)
        );

        $this->assertInstanceOf(SplFixedArray::class, $minutes->getRange(Minutes::FILLED));
        $this->assertEquals($result, $minutes->getRange(Minutes::FILLED));
    }

    /** @test */
    public function unusedInDateTime()
    {
        $object = new Minutes(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 13:00:00'));
        $object->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01  12:31:00'), Minutes::ALLOWED);

        $result = $this->makeRange('0..13', '31..59');
        $result = $this->rangeToDatetime('2020-11-01 12:00:00', $result);

        $this->assertEquals($result, $object->getRangeInDateTime(Minutes::UNUSED));
    }

    /** @test */
    public function allowedInDatetime()
    {
        $object = new Minutes(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 13:00:00'));
        $object->mark(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01  12:31:00'), Minutes::ALLOWED);

        $result = $this->makeRange('14..30');
        $result = $this->rangeToDatetime('2020-11-01 12:00:00', $result);
        
        $this->assertEquals($result, $object->getRangeInDateTime(Minutes::ALLOWED));
    }
}
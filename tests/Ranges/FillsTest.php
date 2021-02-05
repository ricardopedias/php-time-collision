<?php

declare(strict_types=1);

namespace Tests\Ranges;

use DateTime;
use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Exceptions\InvalidDateTimeException;
use TimeCollision\Ranges\Fillings;

class FillsTest extends TestCase
{
    /** @test */
    public function fill()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fill('2020-11-15 10:00', '2020-11-16 11:00');
        $bag->fill('2020-11-15 19:00', '2020-11-16 20:00');

        $this->assertCount(2, $bag->getAll());
        $this->assertCount(0, $bag->getAllCumulatives());
        $this->assertEquals([ new DateTime('2020-11-15 10:00'), new DateTime('2020-11-16 11:00') ], $bag->getAll()[0]);
        $this->assertEquals([ new DateTime('2020-11-15 19:00'), new DateTime('2020-11-16 20:00') ], $bag->getAll()[1]);
    }

    /** @test */
    public function fillSyntaxExceptionStart()
    {
        $this->expectException(InvalidDateTimeException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fill('000000 10,00', '2020-11-16 11:00');
    }

    /** @test */
    public function fillSyntaxExceptionEnd()
    {
        $this->expectException(InvalidDateTimeException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fill('2020-11-15 10:00', '000000 11,00');
    }

    /** @test */
    public function fillException()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The end datetime must be greater than the start datetime of the period');

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fill('2020-11-15 10:00', '2020-11-14 11:00');
    }



    /** @test */
    public function fillCumulative()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fillCumulative('2020-11-15 10:00', '2020-11-16 11:00');
        $bag->fillCumulative('2020-11-15 19:00', '2020-11-16 20:00');

        $this->assertCount(0, $bag->getAll());
        $this->assertCount(2, $bag->getAllCumulatives());
        $this->assertEquals([ new DateTime('2020-11-15 10:00'), new DateTime('2020-11-16 11:00') ], $bag->getAllCumulatives()[0]);
        $this->assertEquals([ new DateTime('2020-11-15 19:00'), new DateTime('2020-11-16 20:00') ], $bag->getAllCumulatives()[1]);
    }

    /** @test */
    public function fillCumulativeSyntaxExceptionStart()
    {
        $this->expectException(InvalidDateTimeException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fillCumulative('000000 10,00', '2020-11-16 11:00');
    }

    /** @test */
    public function fillCumulativeSyntaxExceptionEnd()
    {
        $this->expectException(InvalidDateTimeException::class);

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fillCumulative('2020-11-15 10:00', '000000 11,00');
    }

    /** @test */
    public function fillCumulativeException()
    {
        $this->expectException(InvalidDateTimeException::class);
        $this->expectExceptionMessage('The end datetime must be greater than the start datetime of the period');

        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fillCumulative('2020-11-15 10:00', '2020-11-14 11:00');
    }

    /** @test */
    public function fillBoth()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $bag = new Fillings($collision);
        $bag->fill('2020-11-15 10:00', '2020-11-16 11:00');
        $bag->fillCumulative('2020-11-15 19:00', '2020-11-16 20:00');

        $this->assertCount(1, $bag->getAll());
        $this->assertCount(1, $bag->getAllCumulatives());
        $this->assertEquals([ new DateTime('2020-11-15 10:00'), new DateTime('2020-11-16 11:00') ], $bag->getAll()[0]);
        $this->assertEquals([ new DateTime('2020-11-15 19:00'), new DateTime('2020-11-16 20:00') ], $bag->getAllCumulatives()[0]);
    }
}

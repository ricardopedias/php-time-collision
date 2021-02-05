<?php

declare(strict_types=1);

namespace Tests;

use TimeCollision\Collision;
use TimeCollision\Days\Week;
use TimeCollision\Days\Year;
use TimeCollision\Defaults;
use TimeCollision\Ranges\Chunks;
use TimeCollision\Ranges\Fillings;
use TimeCollision\Ranges\Minutes;

class CollisionInstancesTest extends TestCase
{
    /** @test */
    public function fromWeek()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertInstanceOf(Week::class,$object->fromWeek());
    }

    /** @test */
    public function fromYear()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertInstanceOf(Year::class,$object->fromYear());
    }

    /** @test */
    public function fromDefaults()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertInstanceOf(Defaults::class, $object->fromDefaults());
    }

    /** @test */
    public function fromFillings()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertInstanceOf(Fillings::class, $object->fromFillings());
    }

    /** @test */
    public function fromMinutes()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertInstanceOf(Minutes::class,$object->fromMinutes());
    }

    /** @test */
    public function fromChunks()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $this->assertInstanceOf(Chunks::class,$object->fromChunks());
    }

    /** @test */
    public function forceMinutesRecalculation()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $minutesObjectOne = $object->fromMinutes();

        $object->forceMinutesRecalculation();

        $minutesObjectTwo = $object->fromMinutes();

        $this->assertNotSame($minutesObjectOne, $minutesObjectTwo);
    }
    
}

<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;
use Time\Minutes;

class CollisionRangeInfoTest extends TestCase
{
    /** @test */
    public function unused()
    {
        $object = new Collision(new Minutes($this->dateStart, $this->dateEnd));
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('1..14', Minutes::UNUSED) 
            + $this->period('32..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $object->unused());

        //$this->benchmark();
    }

    /** @test */
    public function allowed()
    {
        $object = new Collision(new Minutes($this->dateStart, $this->dateEnd));
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('15..31', Minutes::ALLOWED);
        
        $this->assertEquals($result, $object->allowed());
    }
}
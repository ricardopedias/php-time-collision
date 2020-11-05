<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;

class GetUsableInfoTest extends TestCase
{
    /** @test */
    public function unused()
    {
        $object = new Collision($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('1..14', Collision::BIT_UNUSED) 
            + $this->period('32..60', Collision::BIT_UNUSED);
        
        $this->assertEquals($result, $object->unused());

        //$this->benchmark();
    }

    /** @test */
    public function allowed()
    {
        $object = new Collision($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('15..31', Collision::BIT_ALLOWED);
        
        $this->assertEquals($result, $object->allowed());
    }
}
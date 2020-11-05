<?php

declare(strict_types=1);

namespace Tests;

use Business\Hours;
use DateTime;

class GetUsableInfoTest extends TestCase
{
    /** @test */
    public function unused()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('1..14', Hours::BIT_UNUSED) 
            + $this->period('32..60', Hours::BIT_UNUSED);
        
        $this->assertEquals($result, $object->unused());

        //$this->benchmark();
    }

    /** @test */
    public function allowed()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('15..31', Hours::BIT_ALLOWED);
        
        $this->assertEquals($result, $object->allowed());
    }
}
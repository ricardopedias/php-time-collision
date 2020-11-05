<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;

class SetUsableTest extends TestCase
{
    /** @test */
    public function usedCollisionOne()
    {
        $object = new Collision($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('1..14', Collision::BIT_UNUSED)
            + $this->period('15..31', Collision::BIT_ALLOWED)
            + $this->period('32..60', Collision::BIT_UNUSED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

    /** @test */
    public function usedCollisionTwo()
    {
        $object = new Collision($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:25:00'));
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:50:00'));

        $result = $this->period('1..9', Collision::BIT_UNUSED)
            + $this->period('10..25', Collision::BIT_ALLOWED)
            + $this->period('26..34', Collision::BIT_UNUSED)
            + $this->period('35..50', Collision::BIT_ALLOWED)
            + $this->period('51..60', Collision::BIT_UNUSED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

    /** @test */
    public function usedCollisionThree()
    {
        $object = new Collision($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:20:00'));
        $object->setUsable(new DateTime('2020-11-01 12:30:00'), new DateTime('2020-11-01 12:40:00'));
        $object->setUsable(new DateTime('2020-11-01 12:50:00'), new DateTime('2020-11-01 13:00:00'));

        $result = $this->period('1..9', Collision::BIT_UNUSED)
            + $this->period('10..20', Collision::BIT_ALLOWED)
            + $this->period('21..29', Collision::BIT_UNUSED)
            + $this->period('30..40', Collision::BIT_ALLOWED)
            + $this->period('41..49', Collision::BIT_UNUSED)
            + $this->period('50..60', Collision::BIT_ALLOWED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

}
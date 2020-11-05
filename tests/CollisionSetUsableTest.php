<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;
use Time\Minutes;

class CollisionSetUsable extends TestCase
{
    /** @test */
    public function setUsableOne()
    {
        $object = new Collision(new Minutes($this->dateStart, $this->dateEnd));
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('1..14', Minutes::UNUSED)
            + $this->period('15..31', Minutes::ALLOWED)
            + $this->period('32..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

    /** @test */
    public function setUsableTwo()
    {
        $object = new Collision(new Minutes($this->dateStart, $this->dateEnd));
        $object->setUsable(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:25:00'));
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:50:00'));

        $result = $this->period('1..9', Minutes::UNUSED)
            + $this->period('10..25', Minutes::ALLOWED)
            + $this->period('26..34', Minutes::UNUSED)
            + $this->period('35..50', Minutes::ALLOWED)
            + $this->period('51..60', Minutes::UNUSED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

    /** @test */
    public function setUsableThree()
    {
        $object = new Collision(new Minutes($this->dateStart, $this->dateEnd));
        $object->setUsable(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:20:00'));
        $object->setUsable(new DateTime('2020-11-01 12:30:00'), new DateTime('2020-11-01 12:40:00'));
        $object->setUsable(new DateTime('2020-11-01 12:50:00'), new DateTime('2020-11-01 13:00:00'));

        $result = $this->period('1..9', Minutes::UNUSED)
            + $this->period('10..20', Minutes::ALLOWED)
            + $this->period('21..29', Minutes::UNUSED)
            + $this->period('30..40', Minutes::ALLOWED)
            + $this->period('41..49', Minutes::UNUSED)
            + $this->period('50..60', Minutes::ALLOWED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

}
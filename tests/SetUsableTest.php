<?php

declare(strict_types=1);

namespace Tests;

use Business\Hours;
use DateTime;

class SetUsableTest extends TestCase
{
    /** @test */
    public function usedHoursOne()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:15:00'), new DateTime('2020-11-01 12:31:00'));

        $result = $this->period('1..14', Hours::BIT_UNUSED)
            + $this->period('15..31', Hours::BIT_ALLOWED)
            + $this->period('32..60', Hours::BIT_UNUSED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

    /** @test */
    public function usedHoursTwo()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:25:00'));
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:50:00'));

        $result = $this->period('1..9', Hours::BIT_UNUSED)
            + $this->period('10..25', Hours::BIT_ALLOWED)
            + $this->period('26..34', Hours::BIT_UNUSED)
            + $this->period('35..50', Hours::BIT_ALLOWED)
            + $this->period('51..60', Hours::BIT_UNUSED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

    /** @test */
    public function usedHoursThree()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:20:00'));
        $object->setUsable(new DateTime('2020-11-01 12:30:00'), new DateTime('2020-11-01 12:40:00'));
        $object->setUsable(new DateTime('2020-11-01 12:50:00'), new DateTime('2020-11-01 13:00:00'));

        $result = $this->period('1..9', Hours::BIT_UNUSED)
            + $this->period('10..20', Hours::BIT_ALLOWED)
            + $this->period('21..29', Hours::BIT_UNUSED)
            + $this->period('30..40', Hours::BIT_ALLOWED)
            + $this->period('41..49', Hours::BIT_UNUSED)
            + $this->period('50..60', Hours::BIT_ALLOWED);
        
        $this->assertEquals($result, $object->range());

        // $this->benchmark();
    }

}
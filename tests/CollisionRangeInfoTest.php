<?php

declare(strict_types=1);

namespace Tests;

use TimeCollision\Collision;
use TimeCollision\Ranges\Minutes;

class CollisionRangeInfoTest extends TestCase
{
    /** @test */
    public function unused()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->fromDefaults()->enablePeriod('12:15', '12:31');

        $result = $this->makeRange('0..13', '31..59');
        $result = $this->rangeToDatetime('2020-11-01 12:00:00', $result);

        $this->assertEquals($result, $object->fromMinutes()->getRangeInDateTime(Minutes::UNUSED));
    }

    /** @test */
    public function allowed()
    {
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->fromDefaults()->enablePeriod('12:15', '12:31');

        $result = $this->makeRange('14..30');
        $result = $this->rangeToDatetime('2020-11-01 12:00:00', $result);
        
        $this->assertEquals($result, $object->fromMinutes()->getRangeInDateTime(Minutes::ALLOWED));
    }
}
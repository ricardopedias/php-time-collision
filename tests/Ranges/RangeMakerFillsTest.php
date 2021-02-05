<?php

declare(strict_types=1);

namespace Tests\Ranges;

use Tests\TestCase;
use TimeCollision\Collision;
use TimeCollision\Ranges\Minutes;
use TimeCollision\Ranges\RangeMaker;

class RangeMakerFillsTest extends TestCase
{
    /** @test */
    public function fill()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00'); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');

        $collision->fromFillings()->fill('2020-11-01 00:00:05', '2020-11-01 00:12:10');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([4,5,6,7,8,9], $minutes->getRange(Minutes::FILLED)->toArray());
    }

    /** @test */
    public function fillCumulative()
    {
        $collision = new Collision('2020-11-01 00:00:00', '2020-11-01 00:15:00'); 
        $collision->fromDefaults()->enablePeriod('00:05', '00:10');
        $collision->fromDefaults()->enablePeriod('00:13', '00:15');

        $collision->fromFillings()->fillCumulative('2020-11-01 00:00:05', '2020-11-01 00:12:10');

        $maker = new RangeMaker($collision);
        $minutes = $maker->makeMinutesRange();

        $this->assertEquals([4,5,6,7,8,9,  12,13,14], $minutes->getRange(Minutes::FILLED)->toArray());
    }
}

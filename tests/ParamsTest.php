<?php

declare(strict_types=1);

namespace Tests;

use Time\Exceptions\InvalidDateException;
use Time\Params;

class ParamsTest extends TestCase
{
    /** @test */
    public function settingDate()
    {
        $bag = new Params();

        $bag->setDate('2020-10-01');
        $bag->setDate('2020-10-02');

        $this->assertCount(2, $bag->getDates());
        $this->assertArrayHasKey('2020-10-01', $bag->getDates());
        $this->assertArrayHasKey('2020-10-02', $bag->getDates());
    }

    /** @test */
    public function settingDateException()
    {
        $this->expectException(InvalidDateException::class);

        $bag = new Params();
        $bag->setDate('0000000');
    }
}

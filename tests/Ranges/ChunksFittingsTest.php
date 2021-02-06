<?php

declare(strict_types=1);

namespace Tests\Ranges;

use DateTime;
use Tests\TestCase;
use TimeCollision\Ranges\Interval;
use TimeCollision\Ranges\Chunks;
use TimeCollision\Ranges\Minutes;

class ChunksFittingsTest extends TestCase
{
    public function dataTests(): array
    {
        // Range das 12:00 às 13:00
        // Liberado das 12:10 às 12:30 = 20 minutos
        //        e das 12:35 às 12:45 = 10 minutos

        $result = []; 

        $result[] = [
            'from' => 5, // minutos
            'result' => [
                0 => [ '2020-11-01 12:10:00', '2020-11-01 12:30:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:45:00' ],
            ]
        ];

        $result[] = [
            'from' => 10, // minutos
            'result' => [
                0 => [ '2020-11-01 12:10:00', '2020-11-01 12:30:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:45:00' ],
            ]
        ];

        $result[] = [
            'from' => 15, // minutos
            'result' => [
                0 => [ '2020-11-01 12:10:00', '2020-11-01 12:30:00' ],
            ]
        ];

        $result[] = [
            'from' => 20, // minutos
            'result' => [
                0 => [ '2020-11-01 12:10:00', '2020-11-01 12:30:00' ],
            ]
        ];

        $result[] = [
            'from' => 25, // minutos
            'result' => []
        ];

        return $result;
    }

    /** 
     * @test 
     * @dataProvider dataTests
     */
    public function fillables($from, $result)
    {
        $range12001300 = $this->makeRangeObject();

        $chunksObject = new Chunks($range12001300);
        
        // Transforma os itens de $result em Interval
        $result = array_map(function($chunk){
            return new Interval($chunk[0], $chunk[1]);
        }, $result);

        // periodos onde cabem $from minutos
        $this->assertEquals($result, $chunksObject->getFittings($from));
    }

    private function makeRangeObject(): Minutes
    {
        $rangeObject = new Minutes(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 13:00:00'));

        // cabe 20 minutos
        $rangeObject->mark(new DateTime('2020-11-01 12:10:00'), new DateTime('2020-11-01 12:30:00'), Minutes::ALLOWED);

        // cabe 10 minutos
        $rangeObject->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:45:00'), Minutes::ALLOWED);

        return $rangeObject;
    }
}
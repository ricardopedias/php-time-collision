<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Chunks;
use Time\Minutes;

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
        $range_1200_1300 = $this->makeRangeObject();

        $chunksObject = new Chunks($range_1200_1300);
        
        // Transforma os itens de $result em DateTimes
        array_walk($result, function(&$chunk){
            $chunk[0] = new DateTime($chunk[0]);
            $chunk[1] = new DateTime($chunk[1]);
        });

        // periodos onde cabem $from minutos
        $this->assertEquals($result, $chunksObject->fittings($from));
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
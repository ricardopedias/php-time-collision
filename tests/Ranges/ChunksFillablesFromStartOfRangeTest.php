<?php

declare(strict_types=1);

namespace Tests\Ranges;

use DateTime;
use Tests\TestCase;
use TimeCollision\Ranges\Interval;
use TimeCollision\Ranges\Chunks;
use TimeCollision\Ranges\Minutes;

class ChunksFillablesFromStartOfRangeTest extends TestCase
{
    public function dataTests(): array
    {
        // Range das 12:00 às 13:00
        // Liberado das 12:00 às 12:25 e das 12:35 às 12:50

        $result = []; 

        // Testes com base no range das 12:00 às 13:00

        $result[] = [
            'from' => [ '2020-11-01 12:00:00', '2020-11-01 13:00:00'], // range todo
            'result' => [
                0 => [ '2020-11-01 12:00:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:00:00', '2020-11-01 12:45:00'], // inicio do range
            'result' => [
                0 => [ '2020-11-01 12:00:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:45:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:15:00', '2020-11-01 13:00:00'], // fim do range
            'result' => [
                0 => [ '2020-11-01 12:15:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:15:00', '2020-11-01 12:45:00'], // dentro do range
            'result' => [
                0 => [ '2020-11-01 12:15:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:45:00' ],
            ]
        ];

        // Com base nos periodos liberados das 12:00 às 12:25 e das 12:35 às 12:50

        $result[] = [
            'from' => [ '2020-11-01 12:00:00', '2020-11-01 12:50:00'], // todos os minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:00:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:00:00', '2020-11-01 12:40:00'], // inicio dos minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:00:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:40:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:15:00', '2020-11-01 12:50:00'], // fim dos minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:15:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:15:00', '2020-11-01 12:45:00'], // dentro dos minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:15:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:45:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:00:00', '2020-11-01 12:55:00'], // fora dos minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:00:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
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
        $extractedAll = $chunksObject->getFillables($from[0], $from[1]);

        // Transforma os itens de $result em Interval
        $result = array_map(function($chunk){
            return new Interval($chunk[0], $chunk[1]);
        }, $result);

        $this->assertEquals($result, $extractedAll);
    }

    private function makeRangeObject(): Minutes
    {
        $rangeObject = new Minutes(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 13:00:00'));

        // Libera no inicio do range
        $rangeObject->mark(new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 12:25:00'), Minutes::ALLOWED);
        $rangeObject->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:50:00'), Minutes::ALLOWED);

        return $rangeObject;
    }

    public function dataTestsForZeroIndex(): array
    {
        // Range das 12:01 às 13:00
        // Liberado das 12:01 às 12:25 e das 12:35 às 12:50

        $result = []; 

        // Testes com base no range das 12:01 às 13:00

        $result[] = [
            'from' => [ '2020-11-01 12:01:00', '2020-11-01 13:00:00'], // range todo
            'result' => [
                0 => [ '2020-11-01 12:01:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:01:00', '2020-11-01 12:45:00'], // inicio do range
            'result' => [
                0 => [ '2020-11-01 12:01:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:45:00' ],
            ]
        ];

        // Com base nos periodos liberados das 12:01 às 12:25 e das 12:35 às 12:50

        $result[] = [
            'from' => [ '2020-11-01 12:01:00', '2020-11-01 12:50:00'], // todos os minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:01:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:01:00', '2020-11-01 12:40:00'], // inicio dos minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:01:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:40:00' ],
            ]
        ];

        $result[] = [
            'from' => [ '2020-11-01 12:01:00', '2020-11-01 12:55:00'], // fora dos minutos liberados
            'result' => [
                0 => [ '2020-11-01 12:01:00', '2020-11-01 12:25:00' ],
                1 => [ '2020-11-01 12:35:00', '2020-11-01 12:50:00' ],
            ]
        ];

        return $result;
    }

    /** 
     * @test 
     * @dataProvider dataTestsForZeroIndex
     */
    public function fillablesFromZeroIndex($from, $result)
    {
        $range12011300 = new Minutes(new DateTime('2020-11-01 12:01:00'), new DateTime('2020-11-01 13:00:00'));

        // Libera no inicio do range
        $range12011300->mark(new DateTime('2020-11-01 12:01:00'), new DateTime('2020-11-01 12:25:00'), Minutes::ALLOWED);
        $range12011300->mark(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:50:00'), Minutes::ALLOWED);

        $chunksObject = new Chunks($range12011300);
        $extractedAll = $chunksObject->getFillables($from[0], $from[1]);

        // Transforma os itens de $result em Interval
        $result = array_map(function($chunk){
            return new Interval($chunk[0], $chunk[1]);
        }, $result);

        $this->assertEquals($result, $extractedAll);
    }
}
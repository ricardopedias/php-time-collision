<?php

declare(strict_types=1);

namespace Tests\Ranges;

use DateTime;
use Tests\TestCase;
use TimeCollision\Ranges\Minutes;

class MinutesMarkTest extends TestCase
{
    public function rangeProvider()
    {
        return [
            // PREENCHE PARCIAMENTE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'),
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // alloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // filleds
                [
                    [new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00')],
                ],
                // result
                $this->makeRange('24..29') // Só libera até 12:30
            ],

            // NÃO PREENCHE, QUANDO DENTRO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'),
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // alloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // filleds
                [
                    // fora das lacunas liberadas
                    [new DateTime('2020-11-01 12:41:00'), new DateTime('2020-11-01 12:50:00')],
                ],
                // result
                $this->makeRange('0..0') // não preenche nada
            ],

            // NÃO PREENCHE A PARTE QUE ESTIVER ANTES DO INICIO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'),
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // alloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // filleds
                [
                    // fora das lacunas liberadas
                    [new DateTime('2020-11-01 11:00:00'), new DateTime('2020-11-01 12:50:00')],
                ],
                // result
                // Ignora das 11:00 às 12:00
                $this->makeRange('19..29', '34..39')
            ],

            // NÃO PREENCHE ANTES DO INICIO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'),
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // alloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // filleds
                [
                    // fora das lacunas liberadas
                    [new DateTime('2020-11-01 10:00:00'), new DateTime('2020-11-01 11:00:00')],
                ],
                // result
                // Ignora antes das 12:00
                $this->makeRange('0..0')
            ],

            // NÃO PREENCHE A PARTE QUE ESTIVER DEPOIS DO TÉRMINO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'),
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // alloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // filleds
                [
                    // fora das lacunas liberadas
                    [new DateTime('2020-11-01 12:31:00'), new DateTime('2020-11-01 14:00:00')],
                ],
                // result
                // Ignora das 13:00 às 14:00
                $this->makeRange('34..39')
            ],

            // NÃO PREENCHE DEPOIS DO TÉRMINO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'),
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // alloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // filleds
                [
                    // fora das lacunas liberadas
                    [new DateTime('2020-11-01 14:00:00'), new DateTime('2020-11-01 15:00:00')],
                ],
                // result
                // Ignora das 13:00 às 14:00
                $this->makeRange('0..0')
            ],
        ];
    }

    /** @test 
      * @dataProvider rangeProvider
     */
    public function fillMinutes($range, $alloweds, $filleds, $result)
    {
        $minutes = new Minutes($range[0], $range[1]);

        foreach ($alloweds as $date) {
            $minutes->mark($date[0], $date[1], Minutes::ALLOWED);
        }
        
        foreach ($filleds as $date) {
            $minutes->mark($date[0], $date[1], Minutes::FILLED);
        }

        $this->assertEquals($result, $minutes->getRange(Minutes::FILLED));
    }
}
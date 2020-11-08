<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Minutes;

class MinutesFillTest extends TestCase
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
                $this->period('25..30', Minutes::FILLED) // Só libera até 12:30
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
                $this->period('0..0', Minutes::FILLED) // não preenche nada
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
                $this->period('20..30', Minutes::FILLED) + $this->period('35..40', Minutes::FILLED)
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
                $this->period('0..0', Minutes::FILLED)
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
                $this->period('35..40', Minutes::FILLED)
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
                $this->period('0..0', Minutes::FILLED)
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

        $this->assertEquals($result, $minutes->range(Minutes::FILLED));
    }
}
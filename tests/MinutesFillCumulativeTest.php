<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Minutes;

class MinutesFillCumulativeTest extends TestCase
{
    public function rangeProvider()
    {
        return [
            // DENTRO DO RANGE: PREENCHE TODOS OS MINUTOS
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
                    // 10 minutos
                    [new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:32:00')],
                ],
                // result
                $this->makeRange(
                    '24..29', // libera até 12:30
                    '34..35' // e continua de 12:35
                )
                // function($minutes) {
                //     var_dump($minutes->range(Minutes::FILLED));
                //     die;
                // }
            ],

            // DENTRO DO RANGE: NÃO PREENCHE, POIS NÃO HÁ MINUTOS LIBERADOS APÓS 12:40
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

            // FORA DO RANGE:  NÃO PREENCHE A PARTE QUE ESTIVER ANTES DO INICIO DO RANGE
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
                    [new DateTime('2020-11-01 11:00:00'), new DateTime('2020-11-01 12:10:00')],
                ],
                // result
                // Ignora das 11:00 às 11:59, sobrando 10 minutos
                $this->makeRange('19..29'), // libera até 12:30
                // function($minutes) {
                //     var_dump($minutes->range(Minutes::FILLED), $this->period('20..30', Minutes::FILLED));
                //     die;
                // }
            ],

            // FORA DO RANGE: NÃO PREENCHE A PARTE QUE ESTIVER DEPOIS DO TÉRMINO DO RANGE
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
                    [new DateTime('2020-11-01 12:32:00'), new DateTime('2020-11-01 13:10:00')],
                ],
                // result
                // Ignora das 13:01 às 13:10, sobrando 10 minutos
                $this->makeRange('34..39'),
                // function($minutes) {
                //     var_dump($minutes->range(Minutes::FILLED), $this->period('30..40', Minutes::FILLED));
                //     die;
                // }
            ],
        ];
    }

    /** @test 
      * @dataProvider rangeProvider
     */
    public function fillMinutes($range, $alloweds, $filleds, $result, $debug = null)
    {
        $minutes = new Minutes($range[0], $range[1]);

        foreach ($alloweds as $date) {
            $minutes->mark($date[0], $date[1], Minutes::ALLOWED);
        }
        
        foreach ($filleds as $date) {
            $minutes->markCumulative($date[0], $date[1], Minutes::FILLED);
        }

        if ($debug !== null) {
            $debug($minutes);
        }

        $this->assertEquals($result, $minutes->range(Minutes::FILLED));
    }
}
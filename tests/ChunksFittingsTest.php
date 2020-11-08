<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Chunks;
use Time\Minutes;

class ChunksFittingsTest extends TestCase
{
    public function rangeProvider()
    {
        return [
            // LIBERADOS NO MEIO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'), // <-- 12:00 ZERO
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // allloweds
                [
                    [new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // minutes required
                [
                    5 => [
                        // results
                        20 => [ 20, 30 ],
                        35 => [ 35, 40 ],
                    ],
                    10 => [
                        // results
                        20 => [ 20, 30 ]
                    ],
                    15 => [
                        // results
                        // ...
                    ]
                ]
            ],
            // 1 PERIODO LIBERADO NO INICIO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'), // <-- 12:00 ZERO
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // allloweds
                [
                    [new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 12:30:00')],
                ],
                // minutes required
                [
                    5 => [
                        // results
                        1 => [ 1, 30 ],
                    ],
                    20 => [
                        // results
                        1 => [ 1, 30 ],
                    ],
                    35 => [
                        // results
                        // ...
                    ]
                ]
            ],
            // 2 PERIODOS, O PRIMEIRO LIBERADO NO INICIO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'), // <-- 12:00 ZERO
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // allloweds
                [
                    [new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 12:30:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')],
                ],
                // minutes required
                [
                    5 => [
                        // results
                        1 => [ 1, 30 ],
                        35 => [ 35, 40 ],
                    ],
                    20 => [
                        // results
                        1 => [ 1, 30 ],
                    ],
                    35 => [
                        // results
                        // ...
                    ]
                ]
            ],
            // 1 PERIODO LIBERADO NO TÉRMINO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'), // <-- 12:00 ZERO
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // allloweds
                [
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 13:00:00')],
                ],
                // minutes required
                [
                    5 => [
                        // results
                        35 => [ 35, 60 ]
                    ],
                    20 => [
                        // results
                        35 => [ 35, 60 ],
                    ],
                    25 => [
                        // results
                        35 => [ 35, 60 ],
                    ],
                    30 => [
                        // results
                        // ...
                    ],
                ]
            ],
            // 2 PERIODOS, O SEGUNDO LIBERADO NO TÉRMINO DO RANGE
            [
                // range
                [
                    new DateTime('2020-11-01 12:00:00'), // <-- 12:00 ZERO
                    new DateTime('2020-11-01 13:00:00'),
                ],
                // allloweds
                [
                    [new DateTime('2020-11-01 12:00:00'), new DateTime('2020-11-01 12:31:00')],
                    [new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 13:00:00')],
                ],
                // minutes required
                [
                    30 => [
                        // results
                        1 => [ 1, 31 ]
                    ],
                    20 => [
                        // results
                        1 => [ 1, 31 ],
                        35 => [ 35, 60 ],
                    ],
                    25 => [
                        // results
                        1 => [ 1, 31 ],
                        35 => [ 35, 60 ],
                    ],
                ]
            ],
        ];
    }

    /** @test 
      * @dataProvider rangeProvider
     */
    public function fittings($range, $alloweds, $requireds)
    {
        $rangeObject = new Minutes($range[0], $range[1]);
        foreach ($alloweds as $period) {
            $rangeObject->mark($period[0], $period[1], Minutes::ALLOWED);
        }

        $object = new Chunks($rangeObject);

        foreach ($requireds as $minutes => $result) {
            $this->assertEquals($result, $object->fittings($minutes));
        }
    }
}
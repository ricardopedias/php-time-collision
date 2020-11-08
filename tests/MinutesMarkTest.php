<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Minutes;

class MinutesMarkTest extends TestCase
{
    public function rangeProvider()
    {
        return [
            // O MINUTO DO RANGE COMEÇA COM ZERO
            [
                new DateTime('2020-11-01 12:00:00'), // <-- 12:00 ZERO
                new DateTime('2020-11-01 13:00:00'),
                new DateTime('2020-11-01 12:00:00'), // 1 minuto corrido
                new DateTime('2020-11-01 12:30:00'), // 30 minutos corridos
                '1..30' // Começa no minuto 1, libera de 1 a 30 minutos
            ],
            // O MINUTO DO RANGE COMEÇA COM ZERO
            // O TEMPO LIBERADO TENDA INICIAR ANTES DO RANGE
            [
                new DateTime('2020-11-01 12:00:00'), // Começa às 12:00
                new DateTime('2020-11-01 13:00:00'),
                new DateTime('2020-11-01 11:30:00'), // Tenta começar às 11:30, mas é normalizado para 12:00 = 1 minuto corrido
                new DateTime('2020-11-01 12:30:00'), // 30 minutos corridos
                '1..30' // Começa no minuto 1, libera de 1 a 30 minutos
            ],
            // O MINUTO DO RANGE COMEÇA COM NÃO-ZERO
            [
                new DateTime('2020-11-01 12:01:00'), // <--- 12:01 NÃO-ZERO
                new DateTime('2020-11-01 13:00:00'),
                new DateTime('2020-11-01 12:01:00'), // 1 minuto corrido
                new DateTime('2020-11-01 12:31:00'), // 30 minutos corridos
                '1..30' // Começa no minuto 1, libera de 1 a 30 minutos
            ],
            // O MINUTO DO RANGE TERMINA COM ZERO
            [
                new DateTime('2020-11-01 12:00:00'), 
                new DateTime('2020-11-01 13:00:00'), // <-- 13:00 ZERO
                new DateTime('2020-11-01 12:30:00'), // 30 minutos corridos
                new DateTime('2020-11-01 13:00:00'), // 60 minutos corridos
                '30..60' // Começa no minuto 1,  libera de 30 a 60 minutos
            ],
            // O MINUTO DO RANGE TERMINA COM ZERO
            // O TEMPO LIBERADO TENDA TERMINAR DEPOIS DO RANGE
            [
                new DateTime('2020-11-01 12:00:00'), 
                new DateTime('2020-11-01 13:00:00'), // Termina às 13:00
                new DateTime('2020-11-01 12:30:00'), // 30 minutos corridos
                new DateTime('2020-11-01 13:30:00'), // Tenta começar às 13:30, mas é normalizado para 13:00 = 60 minutos corridos
                '30..60' // Começa no minuto 1,  libera de 30 a 60 minutos
            ],
            // O MINUTO DO RANGE TERMINA COM NÃO-ZERO
            [
                new DateTime('2020-11-01 12:00:00'), 
                new DateTime('2020-11-01 13:01:00'), // <--- 13:01 NÃO-ZERO
                new DateTime('2020-11-01 12:31:00'), // 31 minutos corridos
                new DateTime('2020-11-01 13:01:00'), // 61 minutos corridos
                '31..61' // Começa no minuto 1, libera de 31 a 61 minutos
            ],
        ];
    }

    /** @test 
      * @dataProvider rangeProvider
     */
    public function allowMinutes($rangeStart, $rangeEnd, $markStart, $markEnd, $result)
    {
        $minutes = new Minutes($rangeStart, $rangeEnd);
        $minutes->mark($markStart, $markEnd, Minutes::ALLOWED);

        $result = $this->period($result, Minutes::ALLOWED) ;
        $this->assertEquals($result, $minutes->range(Minutes::ALLOWED));
    }
}
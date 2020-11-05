<?php

declare(strict_types=1);

namespace Tests;

use Business\Hours;
use DateTime;

class FillTest extends TestCase
{
    /** @test */
    public function fillCropped()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        $object->fill(new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00'));

        // periodo 1: insere do 25 ao 30... 
        // ignora o restante até 34 - porque não faz parte dos ranges liberados
        $result = $this->period('25..30', Hours::BIT_FILLED); 
        $this->assertEquals($result, $object->filled());

        // $this->benchmark($object);
    }

    /** @test */
    public function fillCumulative()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        $object->fill(new DateTime('2020-11-01 12:25:00'), new DateTime('2020-11-01 12:34:00'), true);

        // periodo 1: insere do 25 ao 30 - o que cabe
        // ignora o que não faz parte dos ranges liberados e guarda os minutos de 30 a 34 = 4 minutos
        // periodo 2: insere os 4 minutos guardados = 35 ao 39
        $result = $this->period('25..30', Hours::BIT_FILLED) + $this->period('35..39', Hours::BIT_FILLED); 
        $this->assertEquals($result, $object->filled());
    }

    /** @test */
    public function noFillInsideRange()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        // Tenta preencher fora do período disponível
        $object->fill(new DateTime('2020-11-01 12:41:00'), new DateTime('2020-11-01 12:50:00'));
        $this->assertEquals([], $object->filled());
    }

    /** @test */
    public function noFillStartBeforeRange()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        // Tenta preencher fora do período disponível
        $start = clone $this->dateStart;
        $start->modify('-1 hour');

        // só para conferir os horários
        $this->assertEquals('11:00', $start->format('H:i'));
        $this->assertEquals('12:00', $this->dateStart->format('H:i'));

        $object->fill($start, new DateTime('2020-11-01 12:50:00'));
        $this->assertEquals([], $object->filled());
    }

    /** @test */
    public function noFillEndAfterRange()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        // Tenta preencher fora do período disponível
        $end = clone $this->dateEnd;
        $end->modify('+1 hour');

        // só para conferir os horários
        $this->assertEquals('14:00', $end->format('H:i'));
        $this->assertEquals('13:00', $this->dateEnd->format('H:i'));

        $object->fill(new DateTime('2020-11-01 12:50:00'), $end);
        $this->assertEquals([], $object->filled());
    }

    /** @test */
    public function noFillBeforeRange()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        // Tenta preencher fora do período disponível
        $start = clone $this->dateStart;
        $start->modify('-2 hour');

        $end = clone $this->dateEnd;
        $end->modify('-2 hour');

        // só para conferir os horários
        $this->assertEquals('10:00', $start->format('H:i'));
        $this->assertEquals('11:00', $end->format('H:i'));

        $object->fill($start, $end);
        $this->assertEquals([], $object->filled());
    }

    /** @test */
    public function noFillAfterRange()
    {
        $object = new Hours($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        // Tenta preencher fora do período disponível
        $start = clone $this->dateStart;
        $start->modify('+2 hour');

        $end = clone $this->dateEnd;
        $end->modify('+2 hour');

        // só para conferir os horários
        $this->assertEquals('14:00', $start->format('H:i'));
        $this->assertEquals('15:00', $end->format('H:i'));

        $object->fill($start, $end);
        $this->assertEquals([], $object->filled());
    }

}
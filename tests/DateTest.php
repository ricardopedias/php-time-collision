<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Date;
use Time\Exceptions\InvalidDateException;


class DateTest extends TestCase
{
    /** @test */
    public function constructor()
    {
        $object = new Date('2020-11-01');
        $this->assertEquals(new DateTime('2020-11-01 00:00:00'), $object->day());
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidDateException::class);

        new Date('00000');
    }

    /** @test */
    public function withPeriod()
    {
        $object = new Date('2020-11-01');
        $object->withPeriod('12:00', '14:00');

        $this->assertEquals([
            [ "12:00", "14:00", false]
        ], $object->periods());

        $object->withDefaultPeriod('15:00', '16:00');
        $this->assertEquals([
            [ "12:00", "14:00", false],
            [ "15:00", "16:00", true]
        ], $object->periods());
    }

    /** @test */
    public function withPeriods()
    {
        $object = new Date('2020-11-01');
        $object->withPeriods([
            [ "12:00", "14:00"],
            [ "15:00", "16:00"]
        ]);

        $this->assertEquals([
            [ "12:00", "14:00", false],
            [ "15:00", "16:00", false]
        ], $object->periods());

        // apaga tudo e inicia uma nova lista
        $object->withDefaultPeriods([
            [ "16:30", "17:00"]
        ]);

        $this->assertEquals([
            [ "16:30", "17:00", true]
        ], $object->periods());
    }

    /** @test */
    public function withMixedPeriods()
    {
        $object = new Date('2020-11-01');
        $object->withPeriods([
            [ "12:00", "14:00"],
            [ "15:00", "16:00"]
        ]);

        $this->assertEquals([
            [ "12:00", "14:00", false],
            [ "15:00", "16:00", false]
        ], $object->periods());

        // apaga tudo e inicia uma nova lista
        $object->withDefaultPeriod("16:30", "17:00");

        $this->assertEquals([
            [ "12:00", "14:00", false],
            [ "15:00", "16:00", false],
            [ "16:30", "17:00", true]
        ], $object->periods());
    }

    /** @test */
    public function day()
    {
        $dayDate = new DateTime('2020-11-01 00:00:00');

        $object = new Date('2020-11-01');
        $this->assertEquals($dayDate, $object->day());
        $this->assertEquals('2020-11-01', $object->dayString());
 
        // Horas serão ignoradas e tranformadas em 00:00:00
        $object = new Date('2020-11-01 12:23:56');
        $this->assertEquals($dayDate, $object->day());
        $this->assertEquals('2020-11-01', $object->dayString());
    }

    /** @test */
    public function dayOfWeek()
    {
        $object = new Date('2020-11-01');
        $this->assertEquals(0, $object->dayOfWeek()); // Domingo

        $object = new Date('2020-11-02');
        $this->assertEquals(1, $object->dayOfWeek()); // Segunda

        $object = new Date('2020-11-03');
        $this->assertEquals(2, $object->dayOfWeek()); // Terça

        $object = new Date('2020-11-04');
        $this->assertEquals(3, $object->dayOfWeek()); // Quarta

        $object = new Date('2020-11-05');
        $this->assertEquals(4, $object->dayOfWeek()); // Quinta

        $object = new Date('2020-11-06');
        $this->assertEquals(5, $object->dayOfWeek()); // Sexta

        $object = new Date('2020-11-07');
        $this->assertEquals(6, $object->dayOfWeek()); // Sábado
    }
}

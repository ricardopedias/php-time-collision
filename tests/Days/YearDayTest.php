<?php

declare(strict_types=1);

namespace Tests\Days;

use DateTime;
use Tests\TestCase;
use TimeCollision\Days\YearDay;
use TimeCollision\Exceptions\InvalidYearDayException;
use TimeCollision\Exceptions\InvalidTimeException;

class YearDayTest extends TestCase
{
    /** @test */
    public function constructor()
    {
        $object = new YearDay('2020-11-01');
        $this->assertEquals(new DateTime('2020-11-01 00:00:00'), $object->getDay());
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidYearDayException::class);
        new YearDay('00000');
    }

    /** @test */
    public function day()
    {
        $dayDate = new DateTime('2020-11-01 00:00:00');

        $object = new YearDay('2020-11-01');
        $this->assertEquals($dayDate, $object->getDay());
        $this->assertEquals('2020-11-01', $object->getDayString());
 
        // Horas serão ignoradas e tranformadas em 00:00:00
        $object = new YearDay('2020-11-01 12:23:56');
        $this->assertEquals($dayDate, $object->getDay());
        $this->assertEquals('2020-11-01', $object->getDayString());
    }

    /** @test */
    public function dayOfWeek()
    {
        $object = new YearDay('2020-11-01');
        $this->assertEquals(0, $object->getDayOfWeek()); // Domingo

        $object = new YearDay('2020-11-02');
        $this->assertEquals(1, $object->getDayOfWeek()); // Segunda

        $object = new YearDay('2020-11-03');
        $this->assertEquals(2, $object->getDayOfWeek()); // Terça

        $object = new YearDay('2020-11-04');
        $this->assertEquals(3, $object->getDayOfWeek()); // Quarta

        $object = new YearDay('2020-11-05');
        $this->assertEquals(4, $object->getDayOfWeek()); // Quinta

        $object = new YearDay('2020-11-06');
        $this->assertEquals(5, $object->getDayOfWeek()); // Sexta

        $object = new YearDay('2020-11-07');
        $this->assertEquals(6, $object->getDayOfWeek()); // Sábado
    }
}

<?php

declare(strict_types=1);

namespace Tests;

use Time\Collision;
use DateTime;

class GetFittingsForTest extends TestCase
{
    /** @test */
    public function getFittings()
    {
        $object = new Collision($this->dateStart, $this->dateEnd);
        $object->setUsable(new DateTime('2020-11-01 12:20:00'), new DateTime('2020-11-01 12:30:00')); // periodo 1
        $object->setUsable(new DateTime('2020-11-01 12:35:00'), new DateTime('2020-11-01 12:40:00')); // periodo 2

        $this->assertTrue(true);
        
        $this->assertEquals([
            20 => [ 20, 30 ]
        ], $object->getFittingsFor(10));

        $this->assertEquals([
            20 => [ 20, 30 ],
            35 => [ 35, 40 ],
        ], $object->getFittingsFor(5));

        $this->assertEquals([
            // array vazio
        ], $object->getFittingsFor(15));

        // var_dump($object->getFittingsFor(2)); 
        //echo $object->rangeString();
        //die;

        // periodo 1: insere do 25 ao 30... 
        // ignora o restante até 34 - porque não faz parte dos ranges liberados
        // $result = $this->period('25..30', Collision::BIT_FILLED); 
            
        //$this->assertEquals($result, $object->filled());

        
        // $this->benchmark();
    }
}
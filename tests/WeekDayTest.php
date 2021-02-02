<?php

declare(strict_types=1);

namespace Tests;

use Time\WeekDay;
use Time\Exceptions\InvalidTimeException;
use Time\Exceptions\InvalidWeekDayException;

class WeekDayTest extends TestCase
{
    /** @test */
    public function constants()
    {
        $this->assertEquals(0, WeekDay::SUNDAY);
        $this->assertEquals(1, WeekDay::MONDAY);    
        $this->assertEquals(2, WeekDay::TUESDAY);   
        $this->assertEquals(3, WeekDay::WEDNESDAY); 
        $this->assertEquals(4, WeekDay::THURSDAY);  
        $this->assertEquals(5, WeekDay::FRIDAY);    
        $this->assertEquals(6, WeekDay::SATURDAY);  
        $this->assertEquals(7, WeekDay::ALL_DAYS);  
    }

    /** @test */
    public function constructor()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $this->assertEquals(WeekDay::MONDAY, $object->day());
    }

    /** @test */
    public function constructorException()
    {
        $this->expectException(InvalidWeekDayException::class);
        $this->expectExceptionMessage('The day must be 0 to 7, or use Week::???');
        
        new WeekDay(8);
    }

    /** @test */
    public function withPeriod()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('10:00', '11:00');
        $object->withPeriod('14:00', '15:00');

        $this->assertEquals(['10:00', '11:00', false], $object->periods()[0]);
        $this->assertEquals(['14:00', '15:00', false], $object->periods()[1]);
    }

    /** @test */
    public function withDefaultPeriod()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withDefaultPeriod('10:00', '11:00');
        $object->withDefaultPeriod('14:00', '15:00');

        $this->assertEquals(['10:00', '11:00', true], $object->periods()[0]);
        $this->assertEquals(['14:00', '15:00', true], $object->periods()[1]);
    }

    /** @test */
    public function withPeriodAndDefaultPeriod()
    {
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('10:00', '11:00');
        $object->withDefaultPeriod('14:00', '15:00');

        $this->assertEquals(['10:00', '11:00', false], $object->periods()[0]);
        $this->assertEquals(['14:00', '15:00', true], $object->periods()[1]);
    }

    /** @test */
    public function withPeriodSyntaxExceptionStart()
    {
        $this->expectException(InvalidTimeException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('00,00', '00:00');
    }

    /** @test */
    public function withPeriodSyntaxExceptionEnd()
    {
        $this->expectException(InvalidTimeException::class);

        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('00:00', '00,00');
    }

    /** @test */
    public function withPeriodException()
    {
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('The end time must be greater than the start time of the period');
        
        $object = new WeekDay(WeekDay::MONDAY);
        $object->withPeriod('09:00', '08:00');
    }

    // /** @test */
    // public function withPeriods()
    // {
    //     $object = new WeekDay(WeekDay::MONDAY);
    //     $object->withPeriods([
    //         ['10:00', '11:00'],
    //         ['14:00', '15:00']
    //     ]);

    //     $this->assertEquals(['10:00', '11:00', false], $object->periods()[0]);
    //     $this->assertEquals(['14:00', '15:00', false], $object->periods()[1]);
    // }

    // /** @test */
    // public function withPeriodsDefault()
    // {
    //     $object = new WeekDay(WeekDay::MONDAY);
    //     $object->withPeriods([
    //         ['10:00', '11:00'],
    //         ['14:00', '15:00']
    //     ], true);

    //     $this->assertEquals(['10:00', '11:00', true], $object->periods()[0]);
    //     $this->assertEquals(['14:00', '15:00', true], $object->periods()[1]);
    // }

    // /** @test */
    // public function withPeriodsOverwrite()
    // {
    //     $object = new WeekDay(WeekDay::MONDAY);
    //     $object->withPeriods([
    //         ['08:00', '09:00']
    //     ]);

    //     $object->withPeriods([
    //         ['10:00', '11:00'],
    //         ['14:00', '15:00']
    //     ], true);

    //     $this->assertEquals(['10:00', '11:00', true], $object->periods()[0]);
    //     $this->assertEquals(['14:00', '15:00', true], $object->periods()[1]);

    //     $object->withPeriods([
    //         ['08:00', '10:00'],
    //         ['11:00', '12:00']
    //     ]);

    //     $this->assertEquals(['08:00', '10:00', false], $object->periods()[0]);
    //     $this->assertEquals(['11:00', '12:00', false], $object->periods()[1]);
    // }

    // /** @test */
    // public function withPeriodsSyntaxExceptionStart()
    // {
    //     $this->expectException(InvalidTimeException::class);

    //     $object = new WeekDay(WeekDay::MONDAY);
    //     $object->withPeriods([
    //         ['10:00', '11:00'],
    //         ['14,00', '15:00']
    //     ]);
    // }

    // /** @test */
    // public function withPeriodsSyntaxExceptionEnd()
    // {
    //     $this->expectException(InvalidTimeException::class);

    //     $object = new WeekDay(WeekDay::MONDAY);
    //     $object->withPeriods([
    //         ['10:00', '11:00'],
    //         ['14:00', '15,00']
    //     ]);
    // }

    // /** @test */
    // public function withPeriodsException()
    // {
    //     $this->expectException(InvalidTimeException::class);
    //     $this->expectExceptionMessage('The end time must be greater than the start time of the period');
        
    //     $object = new WeekDay(WeekDay::MONDAY);
    //     $object->withPeriods([
    //         ['10:00', '11:00'],
    //         ['14:00', '13:00'] // <--
    //     ]);
    // }

    
}

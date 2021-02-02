<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Time\Collision;
use Time\WeekDay;
use Time\Exceptions\InvalidDateException;
use Time\Exceptions\InvalidWeekDayException;
use Time\Minutes;

class CollisionDisablingDayTest extends TestCase
{
    /** @test */
    public function allowOnlyMonday()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowDefaultPeriod('08:00', '09:00');
        // libera somente WeekDay::MONDAY
        $object->disableDayOfWeek(WeekDay::SUNDAY);    
        $object->disableDayOfWeek(WeekDay::TUESDAY);   
        $object->disableDayOfWeek(WeekDay::WEDNESDAY); 
        $object->disableDayOfWeek(WeekDay::THURSDAY);  
        $object->disableDayOfWeek(WeekDay::FRIDAY);    
        $object->disableDayOfWeek(WeekDay::SATURDAY);  

        // das 8 as 9 do segundo dia: Segunda-feira
        $start = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end = $start + 60;
        $result = $this->makeRange("{$start}..{$end}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
    }

    /** @test */
    public function allowMondayAndAllowDateAfter()
    {
        $object = new Collision('2020-11-01 00:00:00', '2020-11-03 08:30:00');
        $object->allowDefaultPeriod('08:00', '09:00');
        // libera somente WeekDay::MONDAY
        $object->disableDayOfWeek(WeekDay::SUNDAY);    
        $object->disableDayOfWeek(WeekDay::TUESDAY); // desativou 03/11/2020
        $object->disableDayOfWeek(WeekDay::WEDNESDAY); 
        $object->disableDayOfWeek(WeekDay::THURSDAY);  
        $object->disableDayOfWeek(WeekDay::FRIDAY);    
        $object->disableDayOfWeek(WeekDay::SATURDAY);  

        $object->allowDate('2020-11-03'); // reaativou 03/11/2020

        // das 8 as 9 do segundo dia: Segunda-feira
        $start1 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-02 08:00:00')) - 1;
        $end1 = $start1 + 60;
        // das 8 as 8:30 do terceiro dia: Terça-feira
        $start2 = $this->minutesBeetwen(new DateTime('2020-11-01 00:00:00'), new DateTime('2020-11-03 08:00:00')) - 1;
        $end2 = $start2 + 30;
        $result = $this->makeRange("{$start1}..{$end1}", "{$start2}..{$end2}");
        $result = $this->rangeToDatetime('2020-11-01 00:00:00', $result);

        $this->assertEquals($result, $object->minutes()->rangeInDateTime(Minutes::ALLOWED));
    }

    /** @test */
    public function disableDayException()
    {
        $this->expectException(InvalidWeekDayException::class);
        $this->expectExceptionMessage('The day must be 0 to 7, or use Week::???');
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->disableDayOfWeek(8);
    }

    /** @test */
    public function disableDateSyntaxException()
    {
        $this->expectException(InvalidDateException::class);
        
        $object = new Collision('2020-11-01 12:00:00', '2020-11-01 13:00:00');
        $object->disableDate('2020:01');
    }
}

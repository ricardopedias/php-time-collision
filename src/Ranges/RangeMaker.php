<?php

declare(strict_types=1);

namespace TimeCollision\Ranges;

use TimeCollision\Collision;
use TimeCollision\Days\YearDay;
use TimeCollision\Days\WeekDay;

class RangeMaker
{
    protected Collision $collision;

    protected Minutes $minutes;

    public function __construct(Collision $collision)
    {
        $this->collision  = $collision;

        $this->minutes = new Minutes(
            $this->collision->getRangeStart(),
            $this->collision->getRangeEnd()
        );
    }

    public function makeMinutesRange(): Minutes
    {
        $this->applyYearDays();
        $this->applyWeekDays();

        foreach ($this->collision->fromFillings()->getAll() as $times) {
            $this->minutes->mark($times[0], $times[1], Minutes::FILLED);
        }

        foreach ($this->collision->fromFillings()->getAllCumulatives() as $times) {
            $this->minutes->markCumulative($times[0], $times[1], Minutes::FILLED);
        }

        return $this->minutes;
    }

    private function applyYearDays(): void
    {
        $yearDaysList = $this->collision->fromYear()->getAllDays();
        $weekDaysList  = $this->collision->fromWeek()->getAllDays();

        foreach ($yearDaysList as $yearDayObject) {
            $weekDay = $yearDayObject->getDayOfWeek();

            // Dias ativados explicitamente (2020-11-01) têm primazia
            // em relação a dias de semana (Segunda, Terça ...)
            // Se o dia da semana correspondente estiver desativado,
            // o dia explícito ativa-o forçadamente
            if (isset($weekDaysList[$weekDay]) === false) {
                $weekDayObject = new WeekDay($weekDay);
                $weekDayObject->withPeriodsArray($yearDayObject->getPeriodsArray());
                $weekDaysList[$weekDay] = $weekDayObject;
            }

            $weekDayObject = $weekDaysList[$weekDay];

            $this->allowPeriodsInSpecificDay($yearDayObject, $weekDayObject);
        }
    }

    private function applyWeekDays(): void
    {
        $weekDaysList = $this->collision->fromWeek()->getAllDays();
        $disabledYearDays = $this->collision->fromYear()->getAllDisabledDays();
        $daysChunks   = $this->minutes->getChunks()->getDays();

        foreach ($daysChunks as $minute => $day) {
            $day = new YearDay((string)$day->format('Y-m-d'));

            // Dias desativados explicitamente (2020-11-01) devem ser ignorados
            // pois tem primazia em relação a dias de semana (Segunda, Terça ...)
            $disabledIndex = $day->getDayString();
            if (isset($disabledYearDays[$disabledIndex]) === true) {
                continue;
            }

            // marca somente dias da semana liberados
            $weekDay = $day->getDayOfWeek();
            if (isset($weekDaysList[$weekDay]) === true) {
                $current = clone $this->collision->getRangeStart();
                $current->modify("+ {$minute} minutes");
                $dayObject = new YearDay($current->format('Y-m-d H:i'));
                $this->allowPeriodsInSpecificDay($dayObject, $weekDaysList[$weekDay]);
            }
        }
    }

    /**
     * Marca os períodos dos dias liberados para que possam
     * ser usados para preenchimento.
     */
    private function allowPeriodsInSpecificDay(YearDay $specificDay, WeekDay $weekDay): void
    {
        $this->resolveDefaultPeriodsInWeekDay($weekDay);

        $periodsList = $weekDay->getPeriods();

        //var_dump($periods); exit;

        foreach ($periodsList as $period) {
            $periodStart = $period->getStart();
            $periodEnd   = $period->getEnd();

            $open = $specificDay->getDay();
            $open->setTime($periodStart->getHour(), $periodStart->getMinute());

            $close = $specificDay->getDay();
            $close->setTime($periodEnd->getHour(), $periodEnd->getMinute());

            $this->minutes->mark($open, $close, Minutes::ALLOWED);
        }
    }

    /**
     * Especifica os períodos que serão usados para o dia da semana especificado.
     * Caso não tenha sido setado em WeekDay::withPeriod()
     * será usado o padrão setado em DefaultPeriods
     */
    private function resolveDefaultPeriodsInWeekDay(WeekDay $day): void
    {
        if ($day->getPeriods() !== []) {
            return;
        }

        $periodsList = $this->collision->fromDefaults()->getPeriods();
        foreach ($periodsList as $period) {
            $day->withPeriod($period->getStart()->toString(), $period->getEnd()->toString());
        }
    }
}

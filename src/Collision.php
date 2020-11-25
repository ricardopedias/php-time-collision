<?php

declare(strict_types=1);

namespace Time;

use DateTime;

class Collision extends Settings
{
    /**
     * Obtém as lacunas onde o período se encaixa
     * @return array<int, array>
     */
    public function fittingsFor(int $amountMinutes): array
    {
        return $this->minutes()->chunks()->fittings($amountMinutes);
    }

    protected function populateAlgorithm(): void
    {
        $this->populateWeek();
        $this->populateDates();

        // Obtém os dias contidos no período
        $daysChunks = $this->minutes()->chunks()->days();
        foreach ($daysChunks as $minute => $day) {
            $weekDay = (int)$day->format('w');

            // preenche somente dias da semana liberados
            if (isset($this->weekDays[$weekDay]) === true) {
                $current = clone $this->rangeStart;
                $current->modify("+ {$minute} minutes");

                $dayObject = new Day($current->format('Y-m-d H:i'));
                $this->markDayAllowed($this->weekDays[$weekDay], $dayObject);

                foreach ($this->fills as $times) {
                    $this->markFilled($times[0], $times[1]);
                }

                foreach ($this->cumulativeFills as $times) {
                    $this->markFilled($times[0], $times[1], true);
                }
            }
        }
    }

    private function populateWeek(): void
    {
        // Se nenhum dia ou data explícita tiver sido setada,
        // libera a semana toda
        if ($this->weekDays === [] && $this->dates === []) {
            $this->allowAllDays();
            $this->useDefaultWeekDays = true;
        }

        // Remove dias da semana desativados
        foreach($this->disabledWeekDays as $day) {
            if (isset($this->weekDays[$day]) === true) {
                unset($this->weekDays[$day]);
            }
        }
    }

    private function populateDates(): void
    {
        // Setagem de datas específicas
        foreach ($this->dates as $dateObject) {

            $index = $dateObject->dayString();
            if (isset($this->disabledDates[$index]) === true) {
                continue;
            }

            $weekDay = $dateObject->dayOfWeek();

            if (isset($this->weekDays[$weekDay]) === false) {
                $dayObject = new WeekDay($weekDay);
                $this->weekDays[$weekDay] = $dayObject;
            }
            
            $this->markDayAllowed($this->weekDays[$weekDay], $dateObject);
        }
    }

    /**
     * Especifica os períodos que serão usados para o
     * dia da semana especificado.
     * Caso não tenha sido setado em WeekDay::withPeriod()
     * usará o padrão setado com Collision::allowPeriod.
     */
    private function defaultPeriodsIfNot(WeekDay $day): void
    {
        $day->removeDefaults();

        if ($day->periods() !== []) {
            return;
        }

        $day->withPeriods($this->defaultPeriods, true);
    }

    /**
     * Marca os períodos dos dias liberados para que possam
     * ser usados para preenchimento.
     */
    private function markDayAllowed(WeekDay $dayObject, Day $date): void
    {
        $this->defaultPeriodsIfNot($dayObject);

        $periods = $dayObject->periods();

        foreach ($periods as $times) {
            $periodStart = explode(':', $times[0]);
            $periodEnd = explode(':', $times[1]);

            $open = $date->day();
            $open->setTime((int)$periodStart[0], (int)$periodStart[1]);
            
            $close = $date->day();
            $close->setTime((int)$periodEnd[0], (int)$periodEnd[1]);

            // not work minutes
            $this->minutes()->mark($open, $close, Minutes::ALLOWED);
        }
    }

    /**
     * Marca efetivamente o período especificado como preenchido.
     * @param \DateTime $start
     * @param \DateTime $end
     * @param bool $cumulative
     */
    private function markFilled(DateTime $start, DateTime $end, bool $cumulative = false): void
    {
        if ($cumulative === true) {
            $this->minutes()->markCumulative($start, $end, Minutes::FILLED);
            return;
        }
        
        $this->minutes()->mark($start, $end, Minutes::FILLED);
    }
}

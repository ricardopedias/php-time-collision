<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use Time\Exceptions\InvalidDateTimeException;
use Time\Exceptions\InvalidDayException;
use Time\Exceptions\InvalidTimeException;

class Collision
{
    private DateTime $rangeStart;

    private DateTime $rangeEnd;

    private array $days = [];

    private array $dates = [];

    private array $fills = [];

    private array $cumulativeFills = [];

    private array $defaultPeriods = [];

    private bool $useDefaultDays = false;

    private bool $useDefaultPeriods = false;

    private ?Minutes $minutesObject = null;

    public function __construct(string $start, string $end)
    {
        try {
            $start = new DateTime($start);
            $end   = new DateTime($end);
        } catch(Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidDateTimeException('The end date must be greater than the start date of the period');
        }

        if ($end->format('H:i') === '00:00') {
            $end->modify('+ 24 hours');
        }

        $this->rangeStart = $start;
        $this->rangeEnd   = $end;
    }

    /**
     * Marca um determinado dia da semana como utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo 
     * '6' correponde a Sábado e '7' significa a semana toda.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return \Time\Day
     */
    public function allowDay(int $day = Day::MONDAY): Day
    {
        if ($day < 0 || $day > 7) {
            throw new InvalidDayException("The day must be 0 to 7, or use Week::???");
        }

        $this->clearCollisions();

        $dayObject = new Day($day);
        $this->days[$day] = $dayObject;

        return $this->days[$day];
    }

    /**
     * Marca todos os dias da semana como utilizáveis.
     * @return \Time\Collision
     */
    public function allowAllDays(): self
    {   
        foreach([0, 1, 2, 3, 4, 5, 6] as $day) {
            $this->allowDay($day);
        }
        
        return $this;
    }

    /**
     * Marca um determinado período do dia como utilizável.
     * Os dias marcados como utilizáveis receberão os períodos definidos aqui.
     * @param string $startTime Ex: 08:35
     * @param string $endTime Ex: 09:50
     * @return \Time\Day
     */
    public function allowPeriod(string $startTime, string $endTime): self
    {
        $this->clearCollisions();

        try {
            $start = new DateTime("2020-01-10 {$startTime}");
            $end = new DateTime("2020-01-10 {$endTime}");
        } catch(Exception $e) {
            throw new InvalidTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidTimeException('The end time must be greater than the start time of the period');
        }

        $this->defaultPeriods[] = [$startTime, $endTime];
        return $this;
    }

    /**
     * Marca um dia específico como utilizável.
     * @param string $date Um dia específico. Ex: 2020-10-01
     */
    public function allowDate(string $date): self
    {
        $this->clearCollisions();

        try {
            $date = new DateTime($date);
        } catch(Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        $this->dates[] = $date;
        return $this;
    }

    /**
     * Utiliza o período especificado.
     * Por padrão, as horas que colidirem com minutos não 'usáveis' são perdidos.
     * Caso o parâmetro $cumulative for true, os minutos são distribuídos para
     * as lacunas seguintes até acabarem.
     * @param string $start
     * @param string $end
     * @param bool $cumulative
     */
    public function fill(string $start, string $end, bool $cumulative = false): void
    {
        $start = new DateTime($start);
        $end   = new DateTime($end);

        if ($cumulative === true) {
            $this->cumulativeFills[] = [$start, $end];
            return;
        }
        
        $this->fills[] = [$start, $end];
    }

    /**
     * Obtém as lacunas onde o período se encaixa
     * @return array<int, array<int>>
     */
    public function getFittingsFor(int $amountMinutes): array
    {
        return $this->minutes()->chunks()->fittings($amountMinutes);
    }

    /**
     * Devolve o range total de minutos.
     * @return array<int>
     */
    public function range(): array
    {
        return $this->minutes()->range(Minutes::ALL);
    }

    /**
     * Devolve os minutos bloqueados para uso.
     * @return array<int>
     */
    public function unused(): array
    {
        return $this->minutes()->range(Minutes::UNUSED);
    }

    /**
     * Devolve os minutos que podem ser usados.
     * @return array<int>
     */
    public function allowed(): array
    {
        return $this->minutes()->range(Minutes::ALLOWED);
    }

    /**
     * Devolve os minutos usados dentro do horário comercial.
     * @return array<int>
     */
    public function filled(): array
    {
        return $this->minutes()->range(Minutes::FILLED);
    }

    private function minutes(): Minutes
    {
        if ($this->minutesObject === null) {
            $this->minutesObject = new Minutes(
                $this->rangeStart,
                $this->rangeEnd
            );
            $this->populateMinutesCollisions();
        }

        return $this->minutesObject;
    }

    private function populateMinutesCollisions()
    {
        // Se nenhum dia tiver sido setado, libera a semana toda
        // se uma data tiver sido setada, ignora
        if ($this->days === [] && $this->dates === []) {
            $this->allowAllDays();
            $this->useDefaultDays = true;
        }

        // Setagem de datas específicas
        foreach ($this->dates as $date) {
            $weekDay = (int)$date->format('w');

            if (isset($this->days[$weekDay]) === false) {
                $this->allowDay($weekDay);
            }
            $this->markDayAllowed($this->days[$weekDay], $date);
        }

        // Obtém os dias contidos no período
        $daysChunks = $this->minutes()->chunks()->days();
        foreach ($daysChunks as $minute => $day) {
            $weekDay = (int)$day->format('w');

            // preenche somente dias da semana liberados
            if (isset($this->days[$weekDay]) === true) {

                $current = clone $this->rangeStart;
                $current->modify("+ {$minute} minutes");

                $this->markDayAllowed($this->days[$weekDay], $current);

                foreach ($this->fills as $times) {
                    $this->markFilled($times[0], $times[1]);
                }

                foreach ($this->cumulativeFills as $times) {
                    $this->markFilled($times[0], $times[1], true);
                }
            }
        }

        return $this->minutes();
    }

    private function defaultPeriodsIfNot(Day $day)
    {
        $day->removeDefaults();

        if ($day->periods() !== []) {
            return;
        }

        $day->withPeriods($this->defaultPeriods, true);
    }

    /**
     * Reinicia o objeto que calcula os minutos.
     */
    private function clearCollisions(): void
    {
        if ($this->useDefaultDays === true) {
            $this->useDefaultDays = false;
            $this->days = [];
        }

        $this->minutesObject = null;
    }

    private function markDayAllowed(Day $dayObject, DateTime $day): void
    {
        $this->defaultPeriodsIfNot($dayObject);

        $periods = $dayObject->periods();
        foreach($periods as $times) {
            $periodStart = explode(':', $times[0]);
            $open = clone $day;
            $open->setTime((int)$periodStart[0], (int)$periodStart[1]);

            $periodEnd = explode(':', $times[1]);
            $close = clone $day;
            $close->setTime((int)$periodEnd[0], (int)$periodEnd[1]);

            $this->minutes()->mark($open, $close, Minutes::ALLOWED);
        }
    }

    /**
     * Marca efetivamente o período especificado como utilizado.
     * @param string $start
     * @param string $end
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

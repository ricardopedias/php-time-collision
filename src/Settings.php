<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use Time\Exceptions\InvalidDateTimeException;

abstract class Settings
{
    protected DateTime $rangeStart;

    protected DateTime $rangeEnd;

    /** @var array<int, \Time\WeekDay> */
    protected array $weekDays = [];

    /** @var array<int, \DateTime> */
    protected array $dates = [];

    /** @var array<int, array> */
    protected array $fills = [];

    /** @var array<int, array> */
    protected array $cumulativeFills = [];

    /** @var array<int, array> */
    protected array $defaultPeriods = [];

    protected bool $useDefaultWeekDays = false;

    protected ?Minutes $minutesObject = null;

    public function __construct(string $start, ?string $end = null)
    {
        try {
            $start = new DateTime($start);

            if ($end === null) {
                $customEnd = clone $start;
                $customEnd->setTime(23,59);
            }

            $end = $end === null 
                ? $customEnd
                : new DateTime($end);

        } catch (Exception $e) {
            throw new InvalidDateTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidDateTimeException('The end date must be greater than the start date of the period');
        }

        if ($end->format('H:i') === '00:00') {
            $end->modify('+ 24 hours');
        }

        if ($end->format('H:i') === '23:59') {
            $end->modify('+ 1 minute');
        }

        $this->rangeStart = $start;
        $this->rangeEnd   = $end;
    }

    /**
     * Marca um determinado dia da semana como utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo
     * '6' correponde a Sábado e '7' significa a semana toda.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return \Time\WeekDay
     */
    public function allowDay(int $day = WeekDay::MONDAY): WeekDay
    {
        $this->clearCollisions();

        $dayObject = new WeekDay($day);
        $this->weekDays[$day] = $dayObject;

        return $this->weekDays[$day];
    }

    /**
     * Marca todos os dias da semana como utilizáveis.
     * @return self
     */
    public function allowAllDays(): self
    {
        foreach ([0, 1, 2, 3, 4, 5, 6] as $day) {
            $this->allowDay($day);
        }
        
        return $this;
    }

    /**
     * Marca um determinado período do dia como utilizável.
     * Os dias marcados como utilizáveis receberão os períodos definidos aqui.
     * @param string $startTime Ex: 08:35
     * @param string $endTime Ex: 09:50
     * @return self
     */
    public function allowPeriod(string $startTime, string $endTime): self
    {
        $this->clearCollisions();

        // Apenas para validar o período
        (new WeekDay(WeekDay::MONDAY))->withPeriod($startTime, $endTime);

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
        } catch (Exception $e) {
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
     * Reinicia o objeto que calcula os minutos.
     */
    private function clearCollisions(): void
    {
        if ($this->useDefaultWeekDays === true) {
            $this->useDefaultWeekDays = false;
            $this->weekDays = [];
        }

        $this->minutesObject = null;
    }

    public function minutes(): Minutes
    {
        if ($this->minutesObject === null) {
            $this->minutesObject = new Minutes(
                $this->rangeStart,
                $this->rangeEnd
            );
            $this->populateAlgorithm();
        }

        /** @phpstan-ignore-next-line */
        return $this->minutesObject;
    }

    abstract protected function populateAlgorithm(): void;
}

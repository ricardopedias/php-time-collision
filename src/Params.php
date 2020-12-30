<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Time\Exceptions\InvalidDateException;

class Params
{
    /** @var array<int, \Time\WeekDay> */
    protected array $weekDays = [];

    /** @var array<string, \Time\Date> */
    protected array $dates = [];

    /** @var array<string, \Time\Date> */
    protected array $disabledDates = [];

    /** @var array<int, array> */
    protected array $fills = [];

    /** @var array<int, array> */
    protected array $cumulativeFills = [];

    /** @var array<int, array> */
    protected array $defaultPeriods = [];

    public function __construct()
    {
        // por padrão, todos os dias da semana são utilizáveis
        $this->setAllWeekDays();
    }

    /**
     * Marca um determinado dia da semana como utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo
     * '6' correponde a Sábado e '7' significa a semana toda.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return \Time\WeekDay
     */
    public function setWeekDay(int $day = WeekDay::MONDAY): WeekDay
    {
        $dayObject = new WeekDay($day);
        $this->weekDays[$day] = $dayObject;
        return $this->weekDays[$day];
    }

    /**
     * Ativa todos os dias de semana.
     * @return self
     */
    public function setAllWeekDays(): self
    {
        foreach ([0, 1, 2, 3, 4, 5, 6] as $day) {
            $this->setWeekDay($day);
        }
        return $this;
    }

    /**
     * Marca um determinado dia da semana como não-utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo
     * '6' correponde a Sábado e '7' significa a semana toda.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return self
     */
    public function unsetWeekDay(int $day = WeekDay::MONDAY): self
    {
        // Apenas para validar o dia
        new WeekDay($day);

        if (isset($this->weekDays[$day]) === true) {
            unset($this->weekDays[$day]);
        }

        return $this;
    }

    /**
     * Desativa todos os dias de semana.
     * @return self
     */
    public function unsetAllWeekDays(): self
    {
        foreach ([0, 1, 2, 3, 4, 5, 6] as $day) {
            $this->unsetWeekDay($day);
        }
        return $this;
    }

    /**
     * Marca um dia específico como utilizável.
     * @param string $date Um dia específico. Ex: 2020-10-01
     * @return \Time\Date
     */
    public function setDate(string $date): Date
    {
        try {
            $dayObject = new Date($date);
        } catch (InvalidDateException $e) {
            throw new InvalidDateException($e->getMessage());
        }

        $index = $dayObject->dayString();
        $this->dates[$index] = $dayObject;
        return $this->dates[$index];
    }

    /**
     * Marca um dia específico como não-utilizável.
     * @param string $date Um dia específico. Ex: 2020-10-01
     * @return self
     */
    public function unsetDate(string $date): self
    {
        $dayObject = new Date($date);
        $index = $dayObject->dayString();
        if (isset($this->dates[$index])) {
            unset($this->dates[$index]);
        }

        $this->disabledDates[$index] = $dayObject;
        return $this;
    }

    /**
     * Marca um determinado período do dia como utilizável.
     * Os dias marcados como utilizáveis receberão os períodos definidos aqui.
     * @param string $startTime Ex: 08:35
     * @param string $endTime Ex: 09:50
     * @return self
     */
    public function setDefaultPeriod(string $startTime, string $endTime): self
    {
        // Apenas para validar o período
        (new WeekDay(WeekDay::MONDAY))
            ->withPeriod($startTime, $endTime);

        $this->defaultPeriods[] = [$startTime, $endTime];
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
    public function setFilled(string $start, string $end, bool $cumulative = false): void
    {
        $start = new DateTime($start);
        $end   = new DateTime($end);

        if ($cumulative === true) {
            $this->cumulativeFills[] = [$start, $end];
            return;
        }

        $this->fills[] = [$start, $end];
    }

    /** @return array<int, \Time\WeekDay> */
    public function getWeekDays(): array
    {
        return $this->weekDays;
    }

    /** @return array<string, \Time\Date> */
    public function getDates(): array
    {
        return $this->dates;
    }

    /** @return array<string, \Time\Date> */
    public function getDisabledDates(): array
    {
        return $this->disabledDates;
    }

    /** @return array<int, array> */
    public function getDefaultPeriods(): array
    {
        return $this->defaultPeriods;
    }

    /** @return array<int, array> */
    public function getFills(): array
    {
        return $this->fills;
    }

    /** @return array<int, array> */
    public function getCumulativeFills(): array
    {
        return $this->cumulativeFills;
    }
}

<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use Time\Exceptions\InvalidDateTimeException;

class Collision
{
    protected DateTime $rangeStart;

    protected DateTime $rangeEnd;

    protected Params $params;

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
        $this->params     = new Params();
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
        $this->forceRecalculation();
        return $this->params->setDay($day);
    }

    /**
     * Ativa todos os dias de semana.
     * @return self
     */
    public function allowAllDays(): self
    {
        $this->forceRecalculation();
        $this->params->setAllDays();
        return $this;
    }

    /**
     * Marca um determinado dia da semana como não-utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo
     * '6' correponde a Sábado e '7' significa a semana toda.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return self
     */
    public function disableDay(int $day = WeekDay::MONDAY): self
    {
        $this->forceRecalculation();
        $this->params->unsetDay($day);
        return $this;
    }

    /**
     * Desativa todos os dias de semana.
     * @return self
     */
    public function disableAllDays(): self
    {
        $this->forceRecalculation();
        $this->params->unsetAllDays();
        return $this;
    }

    /**
     * Marca um determinado período do dia como utilizável.
     * Os dias marcados como utilizáveis receberão os períodos definidos aqui.
     * @param string $startTime Ex: 08:35
     * @param string $endTime Ex: 09:50
     * @return self
     */
    public function allowDefaultPeriod(string $startTime, string $endTime): self
    {
        $this->forceRecalculation();
        $this->params->setDefaultPeriod($startTime, $endTime);
        return $this;
    }

    /**
     * Marca um dia específico como utilizável.
     * @param string $date Um dia específico. Ex: 2020-10-01
     * @return \Time\Day
     */
    public function allowDate(string $date): Day
    {
        $this->forceRecalculation();
        return $this->params->setDate($date);
    }

    /**
     * Marca um dia específico como não-utilizável.
     * @param string $date Um dia específico. Ex: 2020-10-01
     * @return self
     */
    public function disableDate(string $date): self
    {
        $this->forceRecalculation();
        $this->params->unsetDate($date);
        return $this;
    }

    /**
     * Utiliza o período especificado.
     * O tempo que colidir com minutos não 'usáveis' serão perdidas.
     * @param string $start
     * @param string $end
     */
    public function fill(string $start, string $end): void
    {
        $this->forceRecalculation();
        $this->params->setFilled($start, $end, false);
    }

    /**
     * Utiliza o período especificado.
     * Os minutos são distribuídos para as lacunas seguintes até acabarem.
     * @param string $start
     * @param string $end
     */
    public function fillCumulative(string $start, string $end): void
    {
        $this->forceRecalculation();
        $this->params->setFilled($start, $end, true);
    }

    public function minutes(): Minutes
    {
        if ($this->minutesObject === null) {
            $calculation = new Calculation(
                $this->params,
                $this->rangeStart,
                $this->rangeEnd
            );
            $this->minutesObject = $calculation->populateRange();
        }

        /** @phpstan-ignore-next-line */
        return $this->minutesObject;
    }

    /**
     * Obtém as lacunas onde os minutos especificados se encaixam
     * @return array<int, array>
     */
    public function fittingsFor(int $amountMinutes): array
    {
        return $this->minutes()->chunks()->fittings($amountMinutes);
    }

    /**
     * Reinicia o objeto que calcula os minutos.
     */
    private function forceRecalculation(): void
    {
        $this->minutesObject = null;
    }
}

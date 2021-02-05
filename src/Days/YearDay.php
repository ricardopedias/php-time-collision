<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use DateTime;
use Exception;
use TimeCollision\Exceptions\InvalidPeriodException;
use TimeCollision\Exceptions\InvalidYearDayException;

class YearDay
{
    private DateTime $day;

    /** @var array<int,\TimeCollision\Days\Period> */
    private array $periods = [];

    public function __construct(string $date)
    {
        try {
            $day = new DateTime($date);
            $day->setTime(0, 0, 0);
        } catch (Exception $e) {
            throw new InvalidYearDayException($e->getMessage());
        }

        $this->day = $day;
    }

    /** @return \TimeCollision\Days\YearDay */
    public function withPeriod(string $startTime, string $endTime): self
    {
        $this->periods[] = new Period($startTime, $endTime);
        return $this;
    }

    /**
     * Especifica uma lista de períodos a serem usados neste dia.
     * @param array<int, array> $periods
     * @return \TimeCollision\Days\YearDay
     */
    public function withPeriodsArray(array $periods): self
    {
        $this->periods = [];
        foreach ($periods as $inputPeriod) {
            if (count($inputPeriod) !== 2) {
                throw new InvalidPeriodException('The period provided is invalid');
            }
            $this->withPeriod($inputPeriod[0], $inputPeriod[1]);
        }

        return $this;
    }

    public function getDay(): DateTime
    {
        return clone $this->day;
    }

    public function getDayString(): string
    {
        return $this->getDay()->format('Y-m-d');
    }

    public function getDayOfWeek(): int
    {
        return (int)$this->getDay()->format('w');
    }

    /** @return array<int,\TimeCollision\Days\Period> */
    public function getPeriods(): array
    {
        return $this->periods;
    }

    /** @return array<int, array> */
    public function getPeriodsArray(): array
    {
        $list = [];
        foreach ($this->periods as $period) {
            $list[] = [
                $period->getStart()->toString(),
                $period->getEnd()->toString()
            ];
        }

        return $list;
    }
}

<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use Time\Exceptions\InvalidDateException;
use Time\Exceptions\InvalidTimeException;

class Date
{
    private DateTime $day;

    /** @var array<int, array> */
    private array $periods = [];

    public function __construct(string $date)
    {
        try {
            $day = new DateTime($date);
            $day->setTime(0, 0, 0);
        } catch (Exception $e) {
            throw new InvalidDateException($e->getMessage());
        }

        $this->day = $day;
    }

    public function withDefaultPeriod(string $startTime, string $endTime): self
    {
        try {
            $start = new DateTime("2020-01-10 {$startTime}");
            $end = new DateTime("2020-01-10 {$endTime}");
        } catch (Exception $e) {
            throw new InvalidTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidTimeException('The end time must be greater than the start time of the period');
        }

        $this->periods[] = [$startTime, $endTime, true];
        return $this;
    }

    public function withPeriod(string $startTime, string $endTime): self
    {
        try {
            $start = new DateTime("2020-01-10 {$startTime}");
            $end = new DateTime("2020-01-10 {$endTime}");
        } catch (Exception $e) {
            throw new InvalidTimeException($e->getMessage());
        }

        if ($start > $end) {
            throw new InvalidTimeException('The end time must be greater than the start time of the period');
        }

        $this->periods[] = [$startTime, $endTime, false];
        return $this;
    }

    /**
     * Especifica uma lista de períodos a serem usados neste dia.
     * @param array<int, array> $periods
     * @return \Time\Date
     */
    public function withDefaultPeriods(array $periods): self
    {
        $this->periods = [];
        foreach ($periods as $item) {
            $this->withDefaultPeriod($item[0], $item[1]);
        }

        return $this;
    }

    /**
     * Especifica uma lista de períodos a serem usados neste dia.
     * @param array<int, array> $periods
     * @return \Time\Date
     */
    public function withPeriods(array $periods): self
    {
        $this->periods = [];
        foreach ($periods as $item) {
            $this->withPeriod($item[0], $item[1]);
        }

        return $this;
    }

    public function removeDefaultPeriods(): self
    {
        foreach ($this->periods as $index => $item) {
            if ($item[2] === true) {
                unset($this->periods[$index]);
            }
        }

        return $this;
    }

    public function day(): DateTime
    {
        return clone $this->day;
    }

    public function dayString(): string
    {
        return $this->day()->format('Y-m-d');
    }

    public function dayOfWeek(): int
    {
        return (int)$this->day()->format('w');
    }

    /**
     * @return array<int, array>
     */
    public function periods(): array
    {
        return $this->periods;
    }
}

<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use Exception;
use Time\Exceptions\InvalidTimeException;
use Time\Exceptions\InvalidWeekDayException;

class WeekDay
{
    /** @const Domingo */
    public const SUNDAY    = 0;

    /** @const Segunda-feira */
    public const MONDAY    = 1;

    /** @const Terça-feira */
    public const TUESDAY   = 2;

    /** @const Quarta-feira */
    public const WEDNESDAY = 3;

    /** @const Quinta-feira */
    public const THURSDAY  = 4;

    /** @const Sexta-feira */
    public const FRIDAY    = 5;

    /** @const Sábado */
    public const SATURDAY  = 6;

    public const ALL_DAYS  = 7;

    private int $day = self::MONDAY;

    /** @var array<int, array> */
    private array $periods = [];

    public function __construct(int $day)
    {
        if ($day < 0 || $day > 7) {
            throw new InvalidWeekDayException("The day must be 0 to 7, or use Week::???");
        }

        $this->day = $day;
    }

    public function withPeriod(string $startTime, string $endTime, bool $default = false): self
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

        $this->periods[] = [$startTime, $endTime, $default];
        return $this;
    }

    /**
     * Especifica uma lista de períodos a serem usados neste dia.
     * @param array<int, array> $periods
     * @param bool $default
     * @return \Time\WeekDay
     */
    public function withPeriods(array $periods, bool $default = false): self
    {
        $this->periods = [];
        foreach ($periods as $item) {
            $this->withPeriod($item[0], $item[1], $default);
        }

        return $this;
    }

    public function removeDefaults(): self
    {
        foreach ($this->periods as $index => $item) {
            if ($item[2] === true) {
                unset($this->periods[$index]);
            }
        }

        return $this;
    }

    public function day(): int
    {
        return $this->day;
    }

    /**
     * @return array<int, array>
     */
    public function periods(): array
    {
        return $this->periods;
    }
}

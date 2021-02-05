<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use TimeCollision\Exceptions\InvalidPeriodException;
use TimeCollision\Exceptions\InvalidTimeException;
use TimeCollision\Exceptions\InvalidWeekDayException;

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

    /** @var array<int,\TimeCollision\Days\Period> */
    private array $periods = [];

    public function __construct(int $day)
    {
        if ($day < 0 || $day > 7) {
            throw new InvalidWeekDayException("The day must be 0 to 7, or use Week::???");
        }

        $this->day = $day;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function withPeriod(string $startTime, string $endTime): self
    {
        $this->periods[] = new Period($startTime, $endTime);
        return $this;
    }

    /**
     * Especifica uma lista de períodos a serem usados neste dia.
     * @param array<int, array<string>> $periodsList
     * @return \TimeCollision\Days\WeekDay
     */
    public function withPeriodsArray(array $periodsList): self
    {
        $this->periods = [];
        foreach ($periodsList as $inputPeriod) {
            if (count($inputPeriod) !== 2) {
                throw new InvalidPeriodException('The period provided is invalid');
            }
            $this->withPeriod($inputPeriod[0], $inputPeriod[1]);
        }

        return $this;
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

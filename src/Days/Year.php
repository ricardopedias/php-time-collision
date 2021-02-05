<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use TimeCollision\Collision;
use TimeCollision\Exceptions\InvalidYearDayException;

class Year
{
    protected Collision $collision;

    /** @var array<string, \TimeCollision\Days\YearDay> */
    protected array $yearDays = [];

    /** @var array<string, \TimeCollision\Days\YearDay> */
    protected array $disabledYearDays = [];

    public function __construct(Collision $collision)
    {
        $this->collision = $collision;
    }

    /**
     * Marca um dia específico como utilizável.
     * Um dia específico tem primazia em relação a um dia de semana,
     * ou seja, habilitando um dia específico, o mesmo só poderá ser
     * desabilitado usando disableYearDay().
     * Por exemplo, usar disableWeekDay(WeekDay::MONDAY) não
     * desativará um dia setado especificamente, mesmo que o dia
     * seja uma segunda-feira.
     * @param string $day Um dia específico. Ex: 2020-10-01
     * @return \TimeCollision\Days\YearDay
     */
    public function enableDay(string $day): YearDay
    {
        try {
            $dayObject = new YearDay($day);
        } catch (InvalidYearDayException $e) {
            throw new InvalidYearDayException($e->getMessage());
        }

        $index = $dayObject->getDayString();

        if (isset($this->disabledYearDays[$index])) {
            unset($this->disabledYearDays[$index]);
        }

        $this->yearDays[$index] = $dayObject;
        return $this->yearDays[$index];
    }

    /**
     * Marca um dia específico como não-utilizável.
     * @param string $day Um dia específico. Ex: 2020-10-01
     * @return self
     */
    public function disableDay(string $day): self
    {
        $this->collision->forceMinutesRecalculation();

        $dayObject = new YearDay($day);
        $index = $dayObject->getDayString();
        if (isset($this->yearDays[$index])) {
            unset($this->yearDays[$index]);
        }

        $this->disabledYearDays[$index] = $dayObject;
        return $this;
    }

    /** @return array<string, \TimeCollision\Days\YearDay> */
    public function getAllDays(): array
    {
        return $this->yearDays;
    }

    /** @return array<string, \TimeCollision\Days\YearDay> */
    public function getAllDisabledDays(): array
    {
        return $this->disabledYearDays;
    }
}

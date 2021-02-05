<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use TimeCollision\Collision;

class Week
{
    protected Collision $collision;

    /** @var array<int, \TimeCollision\Days\WeekDay> */
    protected array $weekDays = [];

    public function __construct(Collision $collision)
    {
        $this->collision = $collision;

        // por padrão, todos os dias da semana são utilizáveis
        $this->enableAllDays();
    }

    /**
     * Marca um determinado dia da semana como utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo
     * '6' correponde a Sábado e '7' significa a semana toda.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return \TimeCollision\Days\WeekDay
     */
    public function enableDay(int $day = WeekDay::MONDAY): WeekDay
    {
        $this->collision->forceMinutesRecalculation();

        $dayObject = new WeekDay($day);
        $this->weekDays[$day] = $dayObject;
        return $this->weekDays[$day];
    }

    /**
     * Ativa todos os dias de semana.
     * @return self
     */
    public function enableAllDays(): self
    {
        foreach ([0, 1, 2, 3, 4, 5, 6] as $day) {
            $this->enableDay($day);
        }
        return $this;
    }

    /**
     * Marca um determinado dia da semana como não-utilizável.
     * Os dias são definidos de 0 a 7, sendo que '0' corresponde ao Domingo
     * '6' correponde a Sábado e '7' significa a semana toda.
     * IMPORTANTE: Um dia setado especificamente com enableMonthDay(),
     * tem primazia em relação a um dia de semana, ou seja, habilitando
     * um dia específico, o mesmo só poderá ser desabilitado usando disableMonthDay().
     * Por exemplo, usar disableWeekDay(WeekDay::MONDAY) não
     * desativará um dia setado especificamente, mesmo que o dia
     * seja uma segunda-feira.
     * @param int $day Um dia da semana. Ex: Week::MONDAY
     * @return self
     */
    public function disableDay(int $day = WeekDay::MONDAY): self
    {
        $this->collision->forceMinutesRecalculation();

        // apenas para validar
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
    public function disableAllDays(): self
    {
        foreach ([0, 1, 2, 3, 4, 5, 6] as $day) {
            $this->disableDay($day);
        }
        return $this;
    }

    /** @return array<int, \TimeCollision\Days\WeekDay> */
    public function getAllDays(): array
    {
        return $this->weekDays;
    }

    /** @return array<int, \TimeCollision\Days\WeekDay> */
    public function getAllDisabledDays(): array
    {
        $allWeek = [0,1,2,3,4,5,6];
        $days = array_diff_key($allWeek, $this->weekDays);
        return array_map(fn($day) => new WeekDay($day), $days);
    }
}

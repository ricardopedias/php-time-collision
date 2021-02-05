<?php

declare(strict_types=1);

namespace TimeCollision;

use TimeCollision\Days\Period;

class Defaults
{
    protected Collision $collision;

    /** @var array<int, Period> */
    protected array $periods = [];

    public function __construct(Collision $collision)
    {
        $this->collision = $collision;
    }

    /**
     * Marca um determinado período do dia como utilizável.
     * Os dias marcados como utilizáveis receberão os períodos definidos aqui.
     * @param string $startTime Ex: 08:35
     * @param string $endTime Ex: 09:50
     * @return self
     */
    public function enablePeriod(string $startTime, string $endTime): self
    {
        $this->collision->forceMinutesRecalculation();
        $this->periods[] = new Period($startTime, $endTime);

        return $this;
    }

    /** @return array<int,\TimeCollision\Days\Period> */
    public function getPeriods(): array
    {
        return $this->periods;
    }
}

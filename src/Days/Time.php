<?php

declare(strict_types=1);

namespace TimeCollision\Days;

use DateTime;

class Time
{
    private DateTime $time;

    public function __construct(DateTime $time)
    {
        $this->time = $time;
    }

    /** @return int */
    public function getHour(): int
    {
        return (int)$this->time->format('H');
    }

    /** @return int */
    public function getMinute(): int
    {
        return (int)$this->time->format('i');
    }

    /** @return string */
    public function toString(): string
    {
        return $this->time->format('H:i');
    }
}

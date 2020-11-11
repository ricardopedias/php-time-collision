<?php

declare(strict_types=1);

namespace Time;

use Closure;

class Day
{
    public const SUNDAY    = 0;
    public const MONDAY    = 1;
    public const TUESDAY   = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY  = 4;
    public const FRIDAY    = 5;
    public const STURDAY   = 6;
    public const ALL_DAYS  = 7;

    private int $day = self::MONDAY;

    private array $periods = [];

    public function __construct(int $day)
    {
        $this->day = $day;
    }

    public function withPeriod(string $start, string $end, bool $default = false): self
    {
        // TODO verificar sintaxe HH:MM
        $this->periods[] = [$start, $end, $default];
        return $this;
    }

    public function withPeriods(array $periods, bool $default = false): self
    {
        $this->periods = [];
        foreach($periods as $item) {
            $this->withPeriod($item[0], $item[1], $default);
        }
        
        return $this;
    }

    public function removeDefaults(): self
    {
        foreach($this->periods as $index => $item) {
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

    public function periods(): array
    {
        return $this->periods;
    }
}

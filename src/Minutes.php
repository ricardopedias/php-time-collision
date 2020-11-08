<?php

declare(strict_types=1);

namespace Time;

use DateTime;

class Minutes
{
    public const UNUSED  = -1;
    public const ALLOWED = 0;
    public const FILLED  = 1;
    public const ALL     = 2;

    /** Começo do período */
    private DateTime $start;

    /** Término do período */
    private Datetime $end;

    /**
     * Período completo
     * @var array<int>
     */
    private array $rangeVector = [];

    public function __construct(DateTime $start, Datetime $end)
    {
        $this->start = $start;
        $this->end   = $end;
        
        $this->rangeVector  = array_fill(1, $this->beetwen($start, $end), self::UNUSED);
    }

    /**
     * Devolve o range total de minutos.
     * @return array<int>
     */
    public function range(int $status = self::ALL): array
    {
        if ($status === self::ALL) {
            return $this->rangeVector;
        }

        $onlyStatus = [];
        foreach ($this->rangeVector as $minute => $currentStatus) {
            if ($status === $currentStatus) {
                $onlyStatus[$minute] = $currentStatus;
            }
        }
        return $onlyStatus;
    }

    /**
     * Marca os minutos com o status especificado.
     * @return void
     */
    public function mark(DateTime $start, Datetime $end, int $status = self::ALLOWED): void
    {
        // Inicio deve estar dentro do range
        if ($start < $this->start) {
            $start = $this->start;
        }

        // Término deve estar dentro do range
        if ($end > $this->end) {
            $end = $this->end;
        }

        $startIn = $this->beetwen($this->start, $start);
        // Se o inicio for 0, deve contar o minuto 1
        $startIn = $startIn === 0 ? 1 : $startIn;

        $endIn = $this->beetwen($this->start, $end);

        // Se não houver minutos, não há nada a fazer
        if ($endIn === 0) {
            return;
        }

        

        for ($x = $startIn; $x <= $endIn; $x++) {
            if ($status === self::ALLOWED || $this->rangeVector[$x] !== self::UNUSED) {
                $this->rangeVector[$x] = $status;
            }
        }
    }

    /**
     * Marca os minutos com o status especificado.
     * de forma acumulativa.
     */
    public function markCumulative(DateTime $start, Datetime $end, int $status = self::ALLOWED): void
    {
        if ($start < $this->start) {
            $start = $this->start;
        }

        if ($end > $this->end) {
            $end = $this->end;
        }

        $startIn = $this->beetwen($this->start, $start);
        // $endIn = $this->beetwen($this->start, $end);

        $settedCount = 0;
        $settedLimit = $this->beetwen($start, $end);
        
        foreach ($this->rangeVector as $minute => $currentBit) {
            // Minutos anteriores serão ignorados
            if ($minute < $startIn) {
                continue;
            }

            // Se todos os minutos foram setados
            if ($settedCount > $settedLimit + 1) {
                break;
            }

            // Marca o minuto no range geral
            if ($status === self::ALLOWED || $currentBit !== self::UNUSED) {
                $this->rangeVector[$minute] = $status;
                $settedCount++;
            }
        }
    }

    private function beetwen(DateTime $start, Datetime $end): int
    {
        $amount = $start->diff($end);

        $minutes  = $amount->days * 24 * 60;
        $minutes += $amount->h * 60;
        $minutes += $amount->i;

        return $minutes;
    }
}

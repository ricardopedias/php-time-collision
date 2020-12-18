<?php

declare(strict_types=1);

namespace Time;

use DateTime;
use SplFixedArray;

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
     * @var SplFixedArray<int>
     */
    private SplFixedArray $rangeVector;

    public function __construct(DateTime $start, Datetime $end)
    {
        $this->start = $start;
        $this->end   = $end;
        
        $vector = array_fill(0, $this->beetwen($start, $end), self::UNUSED);
        $this->rangeVector = SplFixedArray::fromArray($vector);
    }

    public function startRange(): DateTime
    {
        return $this->start;
    }

    public function endRange(): DateTime
    {
        return $this->end;
    }

    /**
     * Devolve o range total de minutos, começando com zero.
     * @return SplFixedArray<int, int>
     */
    public function range(int $status = self::ALL): SplFixedArray
    {
        if ($status === self::ALL) {
            return $this->rangeVector;
        }

        $onlyStatus = [];
        foreach ($this->rangeVector as $index => $currentStatus) {
            if ($status === $currentStatus) {
                $onlyStatus[] = $index;
            }
        }
        return SplFixedArray::fromArray($onlyStatus);
    }

    /**
     * Devolve os minutos bloqueados para uso.
     * @return SplFixedArray<int>
     */
    public function unused(): SplFixedArray
    {
        return (new Chunks($this))->unused();
    }

    /**
     * Devolve os minutos que podem ser usados.
     * @return SplFixedArray<int>
     */
    public function allowed(): SplFixedArray
    {
        return (new Chunks($this))->allowed();
    }

    /**
     * Devolve os minutos usados dentro do horário comercial.
     * @return SplFixedArray<int>
     */
    public function filled(): SplFixedArray
    {
        return (new Chunks($this))->filled();
    }

    /**
     * Marca os minutos com o status especificado.
     * Possibilidades para $status são:
     *  Minutes::ALLOWED
     *  Minutes::FILLED
     *  Minutes::UNUSED
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

        $startIn = $this->beetwen($this->start, $start) - 1;
        $endIn   = $this->beetwen($this->start, $end) - 1;

        // Se não houver minutos, não há nada a fazer
        if ($endIn === -1) {
            return;
        }

        for ($x = $startIn; $x <= $endIn; $x++) {
            if (isset($this->rangeVector[$x]) === false) {
                continue;
            }

            if ($status === self::ALLOWED || $this->rangeVector[$x] !== self::UNUSED) {
                $this->rangeVector[$x] = $status;
            }
        }
    }

    /**
     * Marca os minutos com o status especificado.
     * de forma acumulativa.
     * Possibilidades para $status são:
     *  Minutes::ALLOWED
     *  Minutes::FILLED
     *  Minutes::UNUSED
     * @return void
     */
    public function markCumulative(DateTime $start, Datetime $end, int $status = self::ALLOWED): void
    {
        if ($start < $this->start) {
            $start = $this->start;
        }

        if ($end > $this->end) {
            $end = $this->end;
        }

        $settedCount = 0;
        $settedLimit = $this->beetwen($start, $end);
        $startIn     = $this->beetwen($this->start, $start);
        
        foreach ($this->rangeVector as $index => $currentBit) {
            
            $minute = $index + 1;
            // Minutos anteriores serão ignorados
            if ($minute < $startIn) {
                continue;
            }

            // Se todos os minutos foram setados
            if ($settedCount > $settedLimit) {
                break;
            }

            // Marca o minuto no range geral
            if ($status === self::ALLOWED || $currentBit !== self::UNUSED) {
                $this->rangeVector[$index] = $status;
                $settedCount++;
            }
        }
    }

    public function chunks(): Chunks
    {
        return new Chunks($this);
    }

    public function beetwen(DateTime $start, Datetime $end): int
    {
        // Voltar para o passado é impossível
        if ($end < $start) {
            return 0;
        }

        $amount = $start->diff($end);

        $minutes  = $amount->days * 24 * 60;
        $minutes += $amount->h * 60;
        $minutes += $amount->i;

        return $minutes;
    }
}

<?php

declare(strict_types=1);

namespace TimeCollision\Ranges;

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

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(DateTime $start, Datetime $end)
    {
        $this->start = $start;
        $this->end   = $end;

        $vector = array_fill(0, $this->getMinutesBetween($start, $end), self::UNUSED);
        $this->rangeVector = SplFixedArray::fromArray($vector);
    }

    public function getStartRange(): DateTime
    {
        return $this->start;
    }

    public function getEndRange(): DateTime
    {
        return $this->end;
    }

    /**
     * Devolve o range de minutos, começando com zero.
     * @return SplFixedArray<\DateTime>
     */
    public function getRangeInDateTime(int $type = Minutes::ALL): SplFixedArray
    {
        $list = $this->getRange($type);
        $list = array_map(fn($minute) => $this->getDateTimeFromMinute($minute), $list->toArray());
        return $this->makeDateTimeArray($list);
    }

    /**
     * Devolve o range de minutos, começando com zero.
     * @return SplFixedArray<int>
     */
    public function getRangeVector(): SplFixedArray
    {
        return $this->rangeVector;
    }

    /**
     * Devolve o range de minutos, começando com zero.
     * @return SplFixedArray<int>
     */
    public function getRange(int $status = self::ALL): SplFixedArray
    {
        $onlyStatus = [];
        foreach ($this->rangeVector as $index => $currentStatus) {
            if ($status === $currentStatus || $status === self::ALL) {
                $onlyStatus[] = $index;
            }
        }
        return $this->makeIntArray($onlyStatus);
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

        $startIn = $this->getMinutesBetween($this->start, $start) - 1;
        $endIn   = $this->getMinutesBetween($this->start, $end) - 1;

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
        $settedLimit = $this->getMinutesBetween($start, $end);
        $startIn     = $this->getMinutesBetween($this->start, $start);

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

    public function getChunks(): Chunks
    {
        return new Chunks($this);
    }

    public function getMinutesBetween(DateTime $start, Datetime $end): int
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

    public function getDateTimeFromMinute(int $minute): DateTime
    {
        $moment = clone $this->start;
        $moment->modify("+ {$minute} minutes");
        return $moment;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param array<int> $vector
     * @return SplFixedArray<int>
     */
    private function makeIntArray(array $vector): SplFixedArray
    {
        return SplFixedArray::fromArray($vector);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param array<\DateTime> $vector
     * @return SplFixedArray<\DateTime>
     */
    private function makeDatetimeArray(array $vector): SplFixedArray
    {
        return SplFixedArray::fromArray($vector);
    }
}

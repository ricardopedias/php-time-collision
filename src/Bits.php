<?php

declare(strict_types=1);

namespace Business;

use DateTime;

class Bits
{
    public const BIT_UNUSED  = -1;
    public const BIT_ALLOWED = 0;
    public const BIT_FILLED  = 1;
    
    /**
     * Método recursivo. Cada iteração extrai um pedaço 
     * contendo uma sequência de minutos disponíveis
     */
    private function chunkFittings(array $range, int $minutes, array &$result = [])
    {
        $chunk  = [];
        $hasAllowed = false;
        
        foreach($range as $minute => $bit) {
            // Apenas um pedaço é devolvido
            if (isset($chunk[$minute-1]) === false && $chunk !== []) {
                break;
            }

            $isChunck = $bit === self::BIT_ALLOWED // é um horário vago
                && isset($range[$minute-1]) === true; // é uma sequência;

            if ($isChunck === true) {
                $chunk[$minute] = $bit;
                $hasAllowed = true;
            }
        }

        if ($hasAllowed === false) {
            return $result;
        }

        // Verifica se a lacuna cabe os minutos necessários
        $startMinute = (int)key($chunk);
        $endMinute   = (int)array_key_last($chunk);
        if ($minutes <= ($endMinute - $startMinute)) {
            $result[$startMinute] = [$startMinute, $endMinute];
        }

        // Extrai os itens já analisados 
        $startRange = key($range);
        for($minute = $startRange; $minute <= $endMinute; $minute++) {
            unset($range[$minute]);
        }

        // Busca por outros pedaços
        $this->chunkFittings($range, $minutes, $result);
        
        return $result;
    }

    /**
     * Varre o range e devolve apenas os minutos 
     * do bit especificado.
     * @param int $useBit
     */
    private function extractRangePeriods(int $useBit)
    {
        $onlyBit     = [];
        foreach($this->rangeVector as $minute => $bit) {
            if ($useBit === $bit) {
                $onlyBit[$minute] = $bit;
            }
        }
        return $onlyBit;
    }

    private function minutesBeetwen(DateTime $start, Datetime $end): int
    {
        $amount = $start->diff($end);

        $minutes  = $amount->days * 24 * 60;
        $minutes += $amount->h * 60;
        $minutes += $amount->i;

        return $minutes;
    }

    /**
     * Marca os minutos e devolve o que sobrou.
     * @return array<string, array<int>>
     */
    private function markBits(DateTime $start, Datetime $end, int $bit = self::BIT_ALLOWED): void
    {
        if ($start < $this->start) {
            $start = $this->start;
        }

        if ($end > $this->end) {
            $end = $this->end;
        }

        $startIn = $this->minutesBeetwen($this->start, $start);
        $endIn = $this->minutesBeetwen($this->start, $end);

        // Se não houver minutos, não há nada a fazer
        if ($startIn === 0 || $endIn === 0) {
            return;
        }

        for ($x = $startIn; $x <= $endIn; $x++) {
            if ($bit === self::BIT_ALLOWED || $this->rangeVector[$x] !== self::BIT_UNUSED) {
                $this->rangeVector[$x] = $bit;
            }
        }
    }

    /**
     * Marca os minutos e devolve o que sobrou.
     */
    private function markBitsCumulative(DateTime $start, Datetime $end, int $bit = self::BIT_ALLOWED): void
    {
        if ($start < $this->start) {
            $start = $this->start;
        }

        if ($end > $this->end) {
            $end = $this->end;
        }

        $startIn = $this->minutesBeetwen($this->start, $start);
        $endIn = $this->minutesBeetwen($this->start, $end);

        $settedCount = 0;
        $settedLimit = $this->minutesBeetwen($start, $end);
        
        foreach ($this->rangeVector as $minute => $currentBit) {
            // Minutos anteriores serão ignorados
            if ($minute < $startIn) {
                continue;
            }

            // Se todos os minutos foram setados
            if ($settedCount > $settedLimit+1) {
                break;
            }

            // Marca o minuto no range geral
            if ($bit === self::BIT_ALLOWED || $currentBit !== self::BIT_UNUSED) {
                $this->rangeVector[$minute] = $bit;
                $settedCount++;
            }
        }
    }
}

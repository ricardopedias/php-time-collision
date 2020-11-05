<?php

declare(strict_types=1);

namespace Time;

use DateTime;

class Collision
{
    public const BIT_UNUSED  = -1;
    public const BIT_ALLOWED = 0;
    public const BIT_FILLED  = 1;

    public const ALL_DAYS  = 'all_days';
    public const MONDAY    = 'monday';
    public const TUESDAY   = 'tuesday';
    public const WEDNESDAY = 'wednesday';
    public const THURSDAY  = 'thursday';
    public const FRIDAY    = 'friday';
    public const STURDAY   = 'saturday';
    public const SUNDAY    = 'sunday';

    /** Começo do período */
    private DateTime $start;

    /** Término do período */
    private Datetime $end;

    /** Período completo */
    private array $rangeVector = [];

    public function __construct(DateTime $start, Datetime $end)
    {
        $this->start = $start;
        $this->end   = $end;

        $this->rangeVector  = array_fill(1, $this->minutesBeetwen($start, $end), self::BIT_UNUSED);
    }

    /**
     * Marca o período especificado como utilizável.
     * Ex: horário comercial.
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function setUsable(DateTime $start, Datetime $end): void
    {
        $this->markBits($start, $end, self::BIT_ALLOWED);
    }

    /**
     * Utiliza o período especificado.
     * Por padrão, as horas que colidirem com minutos não 'usáveis' são perdidos.
     * Caso o parâmetro $cumulative for true, os minutos são distribuídos para 
     * as lacunas seguintes até acabarem.
     * @param \DateTime $start
     * @param \DateTime $end
     * @param bool $cumulative
     */
    public function fill(DateTime $start, Datetime $end, bool $cumulative = false): void
    {
        if ($cumulative === true) {
            $this->markBitsCumulative($start, $end, self::BIT_FILLED);
            return;
        }
        
        $this->markBits($start, $end, self::BIT_FILLED);
    }

    /**
     * Obtém as lacunas onde o período se encaixa
     */
    public function getFittingsFor(int $amountMinutes): array
    {
        return $this->chunkFittings($this->rangeVector, $amountMinutes);
    }

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
     * Devolve o range total de minutos.
     * @return array
     */
    public function range(): array
    {
        return $this->rangeVector;
    }

    /**
     * Devolve os horários bloqueados para uso.
     * @return array
     */
    public function unused(): array
    {
        return $this->extractRangePeriods(self::BIT_UNUSED);
    }

    /**
     * Devolve os horários que podem ser usados.
     * @return array
     */
    public function allowed(): array
    {
        return $this->extractRangePeriods(self::BIT_ALLOWED);
    }

    /**
     * Devolve os horários usados dentro do horário comercial.
     * @return array
     */
    public function filled(): array
    {
        return $this->extractRangePeriods(self::BIT_FILLED);
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

<?php

declare(strict_types=1);

namespace Time;

use DateTime;

class Chunks
{
    /** @var array<int> */
    private array $range = [];

    /**
     * @param array<int> $range
     */
    public function __construct(array $range)
    {
        $this->range = $range;
    }

    /**
     * @return array<int, array<int>>
     */
    public function fittings(int $minutes): array
    {
        return $this->chunkFittings($this->range, $minutes);
    }

    /**
     * Método recursivo. Cada iteração extrai um pedaço
     * contendo uma sequência de minutos disponíveis
     * @param array<int> $range
     * @param int $minutes
     * @param array<int, array<int>> $result
     * @return array<int, array<int>>
     */
    private function chunkFittings(array $range, int $minutes, array &$result = []): array
    {
        $chunk  = [];
        $hasAllowed = false;
        
        foreach ($range as $minute => $bit) {
            // Apenas um pedaço é devolvido
            if (isset($chunk[$minute - 1]) === false && $chunk !== []) {
                break;
            }

            $isChunck = $bit === Minutes::ALLOWED // é um horário vago
                && isset($range[$minute - 1]) === true; // é uma sequência;

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
        for ($minute = $startRange; $minute <= $endMinute; $minute++) {
            unset($range[$minute]);
        }

        // Busca por outros pedaços
        $this->chunkFittings($range, $minutes, $result);
        
        return $result;
    }
}

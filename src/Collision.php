<?php

declare(strict_types=1);

namespace Time;

use DateTime;

class Collision
{
    public const ALL_DAYS  = 'all_days';
    public const MONDAY    = 'monday';
    public const TUESDAY   = 'tuesday';
    public const WEDNESDAY = 'wednesday';
    public const THURSDAY  = 'thursday';
    public const FRIDAY    = 'friday';
    public const STURDAY   = 'saturday';
    public const SUNDAY    = 'sunday';

    private Minutes $minutesObject;

    private ?Chunks $chunksObject = null;

    public function __construct(Minutes $minutes)
    {
        $this->minutesObject = $minutes;
    }

    public function minutes(): Minutes
    {
        return $this->minutesObject;
    }

    public function chunks(): Chunks
    {
        if ($this->chunksObject === null) {
            $this->chunksObject = new Chunks($this->minutes()->range());
        }
        return $this->chunksObject;
    }

    /**
     * Marca o período especificado como utilizável.
     * Ex: horário comercial.
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function setUsable(DateTime $start, Datetime $end): void
    {
        $this->minutes()->mark($start, $end, Minutes::ALLOWED);
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
            $this->minutes()->markCumulative($start, $end, Minutes::FILLED);
            return;
        }
        
        $this->minutes()->mark($start, $end, Minutes::FILLED);
    }

    /**
     * Obtém as lacunas onde o período se encaixa
     * @return array<int, array<int>>
     */
    public function getFittingsFor(int $amountMinutes): array
    {
        return $this->chunks()->fittings($amountMinutes);
    }

    /**
     * Devolve o range total de minutos.
     * @return array<int>
     */
    public function range(): array
    {
        return $this->minutes()->range();
    }

    /**
     * Devolve os horários bloqueados para uso.
     * @return array<int>
     */
    public function unused(): array
    {
        return $this->minutes()->range(Minutes::UNUSED);
    }

    /**
     * Devolve os horários que podem ser usados.
     * @return array<int>
     */
    public function allowed(): array
    {
        return $this->minutes()->range(Minutes::ALLOWED);
    }

    /**
     * Devolve os horários usados dentro do horário comercial.
     * @return array<int>
     */
    public function filled(): array
    {
        return $this->minutes()->range(Minutes::FILLED);
    }
}

# Controle gerencimento de minutos

## Preenchimento normal

O preenchimento normal usa os minutos não-disponiveis (Minutes::UNUSED), contabilizando-os juntamente com os minutos liberados (Minutes::ALLOWED).
No entanto, são preenchidos apenas os minutos liberados.

Por exemplo:

Considerando que o construtor receba um range de 12:00 a 12:20:

```
$minutes = new Minutes(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:20:00')
);
```

Internamente, significa que serão contabilizados 20 minutos. 
Na forma de uma timeline ficaria com a seguinte aparência:

```
■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Marcando os períodos de 12:00 a 12:05 e de 12:10 a 12:15 como liberados para uso:

```
$minutes->mark(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:05:00'), 
    Minutes::ALLOWED
);

$minutes->mark(
    new DateTime('2020-01-10 12:10:00'),
    new DateTime('2020-01-10 12:15:00'), 
    Minutes::ALLOWED
);
```

A timeline ficaria com a seguinte aparência:

```
__ __ __ __ __ ■■ ■■ ■■ ■■ __ __ __ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Dessa forma, preenchendo o minutos de 12:00 a 12:08, 
seriam necessários 8 minutos:

```
$minutes->mark(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:08:00'), 
    Minutes::FILLED
);
```

A timeline ficaria com a seguinte aparência:

```
++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ __ __ __ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Perceba que os minutos 06, 07 e 08 foram ignorados, pois não
coincidem com lacunas disponíveis para uso. OU seja, 
apenas 5 minutos foram efetivamente usados.

## Preenchimento acumulativo

O preenchimento acumulativo pula os minutos não-disponiveis (Minutes::UNUSED), contabilizando apenas os minutos liberados (Minutes::ALLOWED).
Os minutos que sobrarem em um período liberado, serão alocados no próximo 
período até que os minutos setados acabem ou o final do range seja atingido.

### Exemplo 1:

Considerando que o construtor receba um range de 12:00 a 12:20:

```
$minutes = new Minutes(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:20:00')
);
```

Internamente, significa que serão contabilizados 20 minutos. 
Na forma de uma timeline ficaria com a seguinte aparência:

```
■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Marcando os períodos de 12:00 a 12:05 e de 12:10 a 12:15 como liberados para uso:

```
$minutes->mark(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:05:00'), 
    Minutes::ALLOWED
);

$minutes->mark(
    new DateTime('2020-01-10 12:10:00'),
    new DateTime('2020-01-10 12:15:00'), 
    Minutes::ALLOWED
);
```

A timeline ficaria com a seguinte aparência:

```
__ __ __ __ __ ■■ ■■ ■■ ■■ __ __ __ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Dessa forma, preenchendo o minutos de 12:00 a 12:08, seriam 
necessários 8 minutos:

```
$minutes->markCumulative(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:08:00'), 
    Minutes::FILLED
);
```

A timeline ficaria com a seguinte aparência:

```
++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ ++ ++ ++ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Os minutos 06, 07 e 08 não se perderam, mas foram alocados 
no próximo período disponível (12:10 a 12:15), de forma
que todos os 8 minutos foram utilizados.

### Exemplo 2:

Suponha que, se no último exemplo, fossem preenchidos os
minutos de 12:00 a 12:15. Seriam necessários 15 minutos:

```
$minutes->markCumulative(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:15:00'), 
    Minutes::FILLED
);
```

A timeline ficaria com a seguinte aparência:

```
++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ ++ ++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ ■■
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Note que a timeline comleta possui apenas 10 minutos 
disponíveis para uso. Ou seja, os outros 5 minutos que faltaram
foram ignorados pois não couberam no range da timeline:

```
... ++ ++ ++ ++ ■■ ■■ ■■ ■■ ■■ xx xx xx xx xx
... 12 13 14 15 16 17 18 19 20 21 22 23 24 25
                               .  .  .  .  .
```
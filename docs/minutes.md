# 6. Algoritmo da classe Minutes

As colisões são tratadas no momento onde os minutos são marcados para uso ou bloqueados.
Nesse processo, usa-se os métodos mark() ou markCumulative(), cujos comportamentos são explicados
a seguir.

## 6.1 Preenchimento comum

O preenchimento comum usa os minutos não-disponiveis (Minutes::UNUSED), contabilizando-os
juntamente com os minutos liberados (Minutes::ALLOWED). No entanto, são preenchidos apenas
os minutos liberados.

Por exemplo:

Considerando que o construtor receba um intervalo de 12:00 a 12:20:

```php
$minutes = new Minutes(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:20:00')
);
```

Internamente, significa que serão contabilizados 20 minutos.
Na forma de uma linha do tempo ficaria com a seguinte aparência:

```php
■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Marcando os períodos de 12:00 a 12:05 e de 12:10 a 12:15 como liberados para uso:

```php
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

A linha do tempo ficaria com a seguinte aparência:

```php
__ __ __ __ __ ■■ ■■ ■■ ■■ __ __ __ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Suponha que seja necessário preencher 8 minutos, em um período de 12:00 a 12:08:

```php
$minutes->mark(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:08:00'), 
    Minutes::FILLED
);
```

A linha do tempo ficaria com a seguinte aparência:

```php
++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ __ __ __ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Perceba que os minutos 06, 07 e 08 foram ignorados, pois não
coincidem com lacunas disponíveis para uso. Ou seja, no período
de 12:00 a 12:08, apenas 5 minutos foram efetivamente usados.

## 6.2 Preenchimento acumulativo

No preenchimento comum, os minutos não-disponiveis (Minutes::UNUSED) são
simplesmente ignorados. Já no preenchimento acumulativo, o processo pula
os minutos não-disponiveis (Minutes::UNUSED), contando os próximos
minutos liberados (Minutes::ALLOWED).

### Exemplo 1

Considerando que o construtor receba um intervalo de 12:00 a 12:20:

```php
$minutes = new Minutes(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:20:00')
);
```

Internamente, significa que serão contabilizados 20 minutos.
Na forma de uma linha do tempo ficaria com a seguinte aparência:

```php
■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Marcando os períodos de 12:00 a 12:05 e de 12:10 a 12:15 como liberados para uso:

```php
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

A linha do tempo ficaria com a seguinte aparência:

```php
__ __ __ __ __ ■■ ■■ ■■ ■■ __ __ __ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Dessa forma, preenchendo o minutos de 12:00 a 12:08, seriam
necessários 8 minutos:

```php
$minutes->markCumulative(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:08:00'), 
    Minutes::FILLED
);
```

A linha do tempo ficaria com a seguinte aparência:

```php
++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ ++ ++ ++ __ __ __ ■■ ■■ ■■ ■■ ■■ 
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Diferente do preenchimento comum, os minutos 06, 07 e 08 não
se perderam, mas foram alocados no próximo período disponível
(12:10 a 12:15), de forma que todos os 8 minutos foram utilizados.

### Exemplo 2

Suponha que, se no último exemplo, fossem preenchidos os
minutos de 12:00 a 12:15. Seriam necessários 15 minutos:

```php
$minutes->markCumulative(
    new DateTime('2020-01-10 12:00:00'),
    new DateTime('2020-01-10 12:15:00'), 
    Minutes::FILLED
);
```

A linha do tempo ficaria com a seguinte aparência:

```php
++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ ++ ++ ++ ++ ++ ++ ■■ ■■ ■■ ■■ ■■
01 02 03 04 05 06 07 08 09 10 11 12 13 14 15 16 17 18 19 20
```

Note que a linha do tempo comleta possui apenas 10 minutos
disponíveis para uso. Ou seja, os outros 5 minutos que faltaram
foram ignorados pois não couberam no intervalo completo da linha do tempo:

```php
... ++ ++ ++ ++ ■■ ■■ ■■ ■■ ■■ xx xx xx xx xx
... 12 13 14 15 16 17 18 19 20 21 22 23 24 25
                               .  .  .  .  .
```

## Sumário

1. [Criando intervalos para manipulação](ranges.md)
2. [Disponibilizando dias e horários utilizáveis](allowance.md)
3. [Encontrando horários disponíveis](search.md)
4. [Usando horários disponíveis](fitting.md)
5. [Arquitetura da biblioteca](architecture.md)
6. [Algoritmo de colisão](minutes.md)
7. [Direto ao ponto - API](api.md)

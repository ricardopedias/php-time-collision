# 7. Direto ao ponto - API

## 7.1. Criação do intervalo

Um intervalo de tempo pode conter várias horas, dias, meses ou até anos, dependendo do problema a ser resolvido.

```php
// 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');
```

```php
// 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10', '2020-01-10');
```

```php
// 12h e 25 minutos no dia 10/01/2020 das 11:35 às 24:00
$object = new Collision('2020-01-10 11:35');
```

```php
// 25 minutos no dia 10/01/2020 das 11:35 às 12:00
$object = new Collision('2020-01-10 11:35', '2020-01-10 12:00');
```

## 7.2. Disponibilizando horários

Para manipular o tempo é preciso disponibilizar períodos, marcando-os como "liberados" para uso. 
O horário comercial de uma empresa pode ser especificada da seguinte forma:

```php
// Libera dois períodos para todos os dias da semana
$object->allowDefaultPeriod('08:00', '12:00');
$object->allowDefaultPeriod('13:00', '18:00');
```

## 7.3. Disponibilizando dias da semana

Por padrão, todos os dias da semana são definidos como "utilizáveis", mas isso
pode ser mudado da seguinte forma:

```php
// Restringe os períodos apenas para os dias úteis
$object->disableDayOfWeek(WeekDay::SATURDAY);
$object->disableDayOfWeek(WeekDay::SUNDAY);
```

```php
// Libera o Sábado para uso
$object->allowDayOfWeek(WeekDay::SATURDAY);
```

```php
// Libera a semana toda, ou seja, reativa o Sábado e o Domingo
$object->allowAllWeekDays();
```

## 7.4. Disponibilizando dias específicos

Além dos dias da semana, em muitos casos, será preciso definir dias especiais, que serão ou não liberados.

```php
// Libera o dia 11, Sábado
$object->allowDate('2020-07-11');
```

```php
// Bloqueia o dia 09, Quinta-feira
$object->disableDate('2020-07-09');
```

## 7.5. Disponibilizando horários para os dias desejados

Existem casos onde é necessário definir um período de trabalho diferente 
do padrão setado com o método allowDefaultPeriod(). 

```php
// Na Quarta-feira, libera apenas meio período
$object->allowDayOfWeek(WeekDay::WEDNESDAY)
    ->withPeriod('08:00', '12:00');
```

```php
// Libera um período diferente para o Sábado
$object->allowDayOfWeek(WeekDay::SATURDAY)
    ->withPeriod('08:00', '11:00')
    ->withPeriod('12:00', '15:00');
```

```php
// Na Quarta-feira, dia 08/07/2020, libera apenas meio período
$object->allowDate('2020-07-08')
    ->withPeriod('08:00', '12:00');
```


## 7.6. Encontrando períodos por minutos

Para encontrar um horário vago de 30 minutos dentro do intervalo:

```php
// Obtém os períodos onde 01h30m (90 minutos) pode se encaixar
$fittings = $object->fittingsFor(90);
```

O resultado será um array contendo todos os períodos disponíveis:

```
[
    0 => [
        0 => DateTime("2020-01-10 15:00:00"),
        1 => DateTime("2020-01-10 18:00:00")
    ]
]
```

## 7.7. Encontrando períodos por extenção

Também é possível buscar os periodos disponíveis em uma extenção específica de tempo dentro do intervalo: 

```php
// Obtém os períodos não preenchidos entre a data inicial e a data final
$fittings = $object->fittingsBetween('2020-10-01 12:00', '2020-10-01 16:00');
```

A variável *$fittings*, do exemplo acima, devolverá o seguinte conteúdo:

```
[
    0 => [
        0 => DateTime("2020-01-10 12:00:00"),
        1 => DateTime("2020-01-10 12:40:00")
    ],
    1 => [
        0 => DateTime("2020-01-10 15:30:00"),
        1 => DateTime("2020-01-10 16:00:00")
    ]
]
```

## 7.8. Preenchendo horários explicitamente

Pode-se preencher os horários da seguinte forma:

```php
// Preenche os períodos com base nos dados de $object->fittingsFor()
$fittings = $object->fill('2020-01-10 15:00:00', '2020-01-10 18:00:00');
```

## 7.9. Preenchendo horários acumulativos

Outra forma de preencher as lacunas disponíveis é usando acumulação de tempo. Nesta modalidade, os minutos que colidirem com espaços indisponíveis **não serão ignorados**, mas usados para preencher as próximas lacunas até que todos os minutos acabem.

```php
// Tenta preencher das 13h às 16h do dia 10/01/2020
$fittings = $object->fillCumulative('2020-01-10 13:00', '2020-01-10 16:00');
```


## 7.10. Obtendo informações de minutos

Para obter as informações armazenadas em cada minuto do intervalo:

```php
// Devolve os minutos bloqueados para uso
$object->minutes()->unused();
```

```php
// Devolve os minutos que podem ser usados
$object->minutes()->allowed();
```

```php
// Devolve os minutos já usados
$object->minutes()->filled();
```

```php
// Devolve o range total de minutos, começando com zero
$object->minutes()->all();
```

## Sumário

1.   [Criando intervalos para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Encontrando horários disponíveis](search.md)
4.   [Usando horários disponíveis](fitting.md)
5.   [Arquitetura da biblioteca](architecture.md)
6.   [Algoritmo de colisão](minutes.md)
7.   [Direto ao ponto - API](api.md)
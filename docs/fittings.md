# 4. Usando horários disponíveis

Como dito no item anterior, uma vez que o [range](ranges.md) foi criado e os [horários de trabalho](allowance.md) foram configurados, pode-se utilizar os espaços vagos no range para alocar horários.

Os espaços marcados como usados tornam-se indisponíveis para os próximos preenchimentos, simulando um agendamento de horários.

Existem duas maneiras de usar horários disponíveis no range, preenchendo horários explicitamente ou de forma acumulativa.

## 4.1. Preenchendo horários explicitamente

Esta forma de preenchimento exige que se saiba com antecedência [onde os horários disponíveis estão](search.md).

Suponhamos que o método [$object->fittingsFor()](search.md) tenha devolvido as seguintes informações:

```
[
    900 => [
        0 => DateTime("2020-01-10 15:00:00"),
        1 => DateTime("2020-01-10 18:00:00")
    ]
]
```

Com esses dados, pode-se usar os horários da seguinte forma:

```php
// Gera um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowDefaultPeriod('13:00', '14:00'); // 60 minutos
$object->allowDefaultPeriod('15:00', '18:00'); // 180 minutos

// Preenche os períodos com base nos dados de $object->fittingsFor()
$fittings = $object->fill('2020-01-10 15:00:00', '2020-01-10 18:00:00');
```

A implementação acima irá marcar o horário das 15h as 18h do dia 10/01/2020 como preenchidos, ficando eles indisponíveis para os próximos preenchimentos.

## 4.2. Estouro de horários preenchidos explicitamente

No item anterior, como foi usado o retorno do método [$object->fittingsFor()](search.md), os horários setados foram definidos exatamente e nenhum minuto se perdeu.

Mas podem existir casos onde se queira alocar um horário maior dentro de uma lacuna menor disponivel no range.

### Exemplo 1

Imagine que no exemplo anterior se tente alocar das 15h às 18h30m. A implementação ficaria assim:

```php
// Gera um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowDefaultPeriod('13:00', '14:00'); // 60 minutos
$object->allowDefaultPeriod('15:00', '18:00'); // 180 minutos

// Tenta preencher das 15h às 19h do dia 10/01/2020
$fittings = $object->fill('2020-01-10 15:00', '2020-01-10 18:30');
```

Por padrão, os tempos definidos são alocados ignorando as colisões com horários indisponíveis. Em outras palavras, a implementação acima irá ignorar os 30 minutos excedentes depois das 18h, porque a partir das 18h não existem lacunas disponíveis.

Será considerado o seguinte:

| Categoria                | Tempo            | Estado              |
| ------------------------ |----------------- | ------------------- |
| Tempo especificado       | das 15h às 18h30 | 3h e 30m total      |
| Lacuna das 15h às 18h    | das 15h às 18h   | 3 horas preenchidas |
| Espaço das 18h as 18h30m | das 18h às 18h30 | **30m ignorados**   |

**Resultado:** 3h preenchidas e 30m ignorados.

### Exemplo 2

Ainda no mesmo cenário, imagine que se tente alocar das 13h às 16h. A implementação ficaria assim:

```php
// Gera um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowDefaultPeriod('13:00', '14:00'); // 60 minutos
$object->allowDefaultPeriod('15:00', '18:00'); // 180 minutos

// Tenta preencher das 13h às 16h do dia 10/01/2020
$fittings = $object->fill('2020-01-10 13:00', '2020-01-10 16:00');
```

Neste novo exemplo, como o algoritmo ignora as colisões com horários indisponíveis, a implementação acima irá considerar o seguinte:

| Categoria              | Tempo            | Estado              |
| ---------------------- |----------------- | ------------------- |
| Tempo especificado     | das 13h às 16h   | 3 horas total       |
| Lacuna das 13h as 14h  | das 13h às 14h   | 1 hora preenchida   |
| Espaço das 14h as 15h  | das 14h às 15h   | **1 hora ignorada** |
| Lacuna das 15h às 18h  | das 15h às 16h   | 1 horas preenchida  |

**Resultado:** 2h preenchidas e 1h ignorada.


## 4.3. Preenchendo horários acumulativos

Outra forma de preencher as lacunas disponíveis é usando acumulação de tempo. Nesta modalidade, os minutos que colidirem com espaços indisponíveis não serão ignorados, mas usados para preencher as próximas lacunas até que todos os minutos acabem.

Imagine o mesmo exemplo anterior, onde se tenta alocar das 13h às 16h. A implementação acumulativa ficaria assim:

```php
// Gera um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowDefaultPeriod('13:00', '14:00'); // 60 minutos
$object->allowDefaultPeriod('15:00', '18:00'); // 180 minutos

// Tenta preencher das 13h às 16h do dia 10/01/2020
$fittings = $object->fillCumulative('2020-01-10 13:00', '2020-01-10 16:00');
```

Neste exemplo, como o algoritmo não ignora as colisões com horários indisponíveis, a implementação acima irá considerar o seguinte:

| Categoria              | Tempo            | Estado              |
| ---------------------- |----------------- | ------------------- |
| Tempo acumulado        | das 13h às 16h   | 3 horas total       |
| Periodo das 13h as 14h | das 13h às 14h   | 1 hora preenchida   |
| Período das 15h às 18h | das 15h às 16h   | 2 horas preenchidas |

**Resultado:** 3h preenchidas e nenhum minuto ignorado.

## Sumário

1.   [Criando ranges para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Encontrando horários disponíveis](search.md)
4.   [Usando horários disponíveis](fitting.md)
5.   [Obtendo informações sobre os horários](informations.md)
6.   [Arquitetura da biblioteca](architecture.md)

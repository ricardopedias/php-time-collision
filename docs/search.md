# 3. Encontrando horários disponíveis

Com o [intervalo](ranges.md) criado e os [horários de trabalho](allowance.md) devidamente configurados, pode-se utilizar os espaços vagos no intervalo para alocar horários.

Mas antes de utilizar espaços de tempo disponíveis no intervalo, pode ser necessário saber onde eles estão e se os minutos necessários cabem neles.

## 3.1. Encontrando períodos por minutos

Suponha que seja necessário encontrar um horário vago de 30 minutos dentro do intervalo. Isso pode ser feito com o método fittingsFor():

```php
// Gera um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowDefaultPeriod('13:00', '14:00'); // 60 minutos
$object->allowDefaultPeriod('15:00', '18:00'); // 180 minutos

// Obtém os períodos onde 01h30m (90 minutos) pode se encaixar
$fittings = $object->fittingsFor(90);
```

O resultado será um array contendo todos os períodos disponíveis onde
1h30m, ou seja, 90 minutos podem ser alocados. A variável *$fittings*, do exemplo acima, devolverá o seguinte conteúdo:

```php
[
    0 => [
        0 => DateTime("2020-01-10 15:00:00"),
        1 => DateTime("2020-01-10 18:00:00")
    ]
]
```

Ou seja, apenas o período das 15:00 às 18:00 pode alocar 01h30m. O valor, constituído de um array com dois elementos, corresponde ao início e ao fim do período.

## 3.2. Encontrando períodos por extenção

Também é possível buscar os periodos disponíveis em uma extenção específica de tempo dentro do intervalo. Isso é feito passando um horário de inicio e fim para o método fittingsBetween().

```php
// Obtém os períodos não preenchidos entre a data inicial e a data final
$fittings = $object->fittingsBetween('2020-10-01 12:00', '2020-10-01 16:00');
```

A variável *$fittings*, do exemplo acima, devolverá o seguinte conteúdo:

```php
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

## Sumário

1. [Criando intervalos para manipulação](ranges.md)
2. [Disponibilizando dias e horários utilizáveis](allowance.md)
3. [Encontrando horários disponíveis](search.md)
4. [Usando horários disponíveis](fitting.md)
5. [Arquitetura da biblioteca](architecture.md)
6. [Algoritmo de colisão](minutes.md)
7. [Direto ao ponto - API](api.md)

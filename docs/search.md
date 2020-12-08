# 3. Encontrando horários disponíveis

Com o [range](ranges.md) criado e os [horários de trabalho](allowance.md) configurados, pode-se utilizar os espaços vagos no range para alocar horários.

Mas antes de utilizar espaços de tempo disponíveis no range, pode ser necessário saber onde eles estão e se os minutos necessários cabem neles.

## 3.1. Encontrando períodos

Suponha que seja necessário encontrar um horário vago de 30 minutos dentro do range. Isso pode ser feito da seguinte forma:

```php
// Gera um range de 24 horas no dia 10/01/2020
$object = new Collision('2020-01-10');

// Libera dois períodos dentro do range
$object->allowDefaultPeriod('13:00', '14:00'); // 60 minutos
$object->allowDefaultPeriod('15:00', '18:00'); // 180 minutos

// Obtém os períodos onde 01h30m pode se encaixar
$fittings = $object->fittingsFor(90);
```

O resultado será um array contendo todos os períodos disponíveis onde
1h30m podem ser alocados. O resultado do exemplo acima devolverá, na variável *"$fittings"*, o seguinte conteúdo:

```
[
    900 => [
        0 => DateTime("2020-01-10 15:00:00"),
        1 => DateTime("2020-01-10 18:00:00")
    ]
]
```

Ou seja, apenas o período das 15:00 às 18:00 podem alocar 01h30m, sendo:

1. O índice **900** é o número de minutos desde o início do range até atingir o início do período;
2. O valor, constituído de um array com dois elementos, corresponde ao início e ao fim dos períodos, ou seja, das 15:00 às 18:00.

## Sumário

1.   [Criando ranges para manipulação](ranges.md)
2.   [Disponibilizando dias e horários utilizáveis](allowance.md)
3.   [Encontrando horários disponíveis](search.md)
4.   [Usando horários disponíveis](fitting.md)
5.   [Obtendo informações sobre os horários](informations.md)
6.   [Arquitetura da biblioteca](architecture.md)

# 5. Arquitetura da biblioteca

O design da biblioteca foi elaborado usando composição, para facilitar a separação de interesses, aumentando a coesão e reduzindo o acoplamento entre as funcionalidades.

A seguir, uma breve explicação sobre as responsabilidades de cada módulo.

![Diagrama de Classes](images/class-diagram.png)

> Diagrama feito com [StarUML](https://staruml.io).

## 5.1. TimeCollision\Collision

Uma fachada que encapsula todas as funcionalidades interessantes para o usuário. Esta é a única classe que o usuário final irá utilizar.

## 5.2. TimeCollision\Defaults

Guarda os parâmetros globais, que são usados como padrões em caso de não serem setados em seus respectivos módulos.

## 5.3. TimeCollision\Days

Pacote onde se contextualizam as funcionalidades para manipulação de dias dentro do intervalo de colisões.

### 5.3.1. TimeCollision\Days\Year

Funcionalidades para manipulações de dias do ano.

### 5.3.2. TimeCollision\Days\YearDay

Representação contendo os dados de um dia específico do ano (1980-01-10, 1980-01-11, ...).

### 5.3.3. TimeCollision\Days\Week

Funcionalidades para manipulações de dias da semana.

### 5.3.4. TimeCollision\Days\WeekDay

Representação contendo os dados de um dia da semana (segunda, terça, ...).

### 5.3.5. TimeCollision\Days\Period

Encapsula a representação de inicio e fim de um período de tempo. Um periodo é baseado em horas, minutos e segundos.

### 5.3.6. TimeCollision\Days\Time

Encapsula a representação de tempo.

## 5.4. TimeCollision\Ranges

Pacote onde se contextualizam as funcionalidades para manipulação do intervalo de minutos.

### 5.4.1. TimeCollision\Ranges\RangeMaker

É responsável pela criação de uma instância da classe Minutes, usando os parâmetros especificados pelo usuario.

### 5.4.2. TimeCollision\Ranges\Minutes

Contém o intervalo com todos os minutos, baseados no período especificado na contrução da classe TimeCollision\Collision.
Os minutos disponíveis podem ser manipulados através de funcionalidades existentes aqui, recebendo marcações de estado que determinam se eles podem ser usados ou não.

Para entender melhor o funcionamento do algoritmo, acesse as [informações exclusivas da classe Minutes](minutes.md).

### 5.4.3. TimeCollision\Ranges\Chunks

Funcionalidades para extração de pedaços de tempo, com base nos estados de cada minuto. Os pedaços são períodos disponíveis para uso dentro do entervalo existente na instância da classe TimeCollision\Ranges\Minutes.

### 5.4.4. TimeCollision\Ranges\Fillings

Funcionalidades para preenchimento de lacunas disponpiveis dentro do entervalo existente na instância da classe TimeCollision\Ranges\Minutes.

### 5.4.5. TimeCollision\Ranges\Interval

Encapsula a representação de inicio e fim de um intervalo de tempo. Um intervalo é baseado em dias, horas, minutos e segundos.

## Sumário

1. [Criando intervalos para manipulação](ranges.md)
2. [Disponibilizando dias e horários utilizáveis](allowance.md)
3. [Encontrando horários disponíveis](search.md)
4. [Usando horários disponíveis](fitting.md)
5. [Arquitetura da biblioteca](architecture.md)
6. [Algoritmo de colisão](minutes.md)
7. [Direto ao ponto - API](api.md)

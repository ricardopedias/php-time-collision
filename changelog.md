# Changelog

Este é o registro contendo as alterações mais relevantes efetuadas no projeto
seguindo o padrão que pode ser encontrado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0).

Para obter o diff para uma versão específica, siga para o final deste documento 
e acesse a URL da versão desejada. Por exemplo, v4.0.0 ... v4.0.1.
As versões seguem as regras do [Semantic Versioning](https://semver.org/lang/pt-BR).

## \[Unreleased]

### Changed

-    Atualização do changelog

## \[0.10.0] - 2020-02-01

### Added

-    Criação da documentação de arquitetura
-    Criação da documentação da API

### Changed

-    Refatoração para nomenclatura mais inteligível
-    Otimização do algoritmo de extração de lacunas de tempo

## \[0.9.0] - 2020-12-30

### Added

-    Adicionados testes para diversas situações
-    Adicionadas novas informações na documentação

### Changed

-    Transferida a responsabilidade sobre informações para a classe Chunks
-    Refatoração da classe Chunks para reaproveitar o algoritmo de extração
-    Renomeação em vários métodos para reduzir a carga mental

### Removed

-    Remoção da análise automática da PSR2 no script do composer

## \[0.8.0] - 2020-12-04

### Added

-    Adicionados testes para diversas situações
-    Criação do conteiner para armazenar os parâmetros de cálculo
-    Criação de método exclusivo para preenchimentos acumulativos

## \[0.7.0] - 2020-11-25

### Added

-    Criação do objeto para encapsulamento de dias específicos
-    Atualização na documentação

### Fixed

-   Correção na devolução nula do objeto de minutos

## \[0.6.0] - 2020-11-11

### Added

-   Aumento na cobertura de testes para a classe Collision
-   Adicionadas exceções personalizadas

### Changed

-   Refatoração para aumentar a manutenibilidade
-   Padronização da formatação do código fonte

## \[0.5.0] - 2020-11-10

### Added

-   Adicionadas funcionalidades de períodos à classe principal
-   Adicionadas exceções personalizadas

## \[0.4.0] - 2020-11-08

### Added

-   Refatoração do construtor de chunks para usar um objeto Minutes
-   Criação de um teste de unidade específico para marcações
-   Criação de documentação com a regra de negócio sobre a timeline de minutos

### Fixed

-   Classe Chunks ignorava o primeiro pedaço quando o minuto inicial e o inicio do range eram iguais
-   Inconsistência ao marcar os minutos iniciais no método Minutes::markCumulative

## \[0.3.0] - 2020-11-06

### Added

-   Atualização do changelog.md

### Fixed

-   Padronização às regras das PSRs 1, 1 e 12
-   Correção das informações no arquivo readme.md

## \[0.2.0] - 2020-11-05

### Added

-   Refatoração para prover composição

## \[0.1.0] - 2020-11-03

### Added

-   Criação da estrutura básica do projeto.
-   Criação das primeiras implementações

## Releases

-   Unreleased <https://github.com/ricardopedias/php-reliability/compare/v0.3.0...HEAD>
-   0.3.0 <https://github.com/ricardopedias/php-time-collision/releases/tag/v0.2.0...v0.3.0>
-   0.2.0 <https://github.com/ricardopedias/php-time-collision/releases/tag/v0.1.0...v0.2.0>
-   0.1.0 <https://github.com/ricardopedias/php-time-collision/releases/tag/v0.1.0>

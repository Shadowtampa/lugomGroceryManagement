# Lugom Grocery Manager (LGM) - API Documentation

## Descrição

O Lugom Grocery Manager (LGM) é um sistema de gerenciamento de despensa familiar. Esta documentação descreve a API RESTful do backend, responsável por fornecer os dados e a lógica de negócios para o sistema.

## Tecnologias Utilizadas

* PHP, Laravel
* Banco de Dados Relacional - MySQL

## Configuração

TODO

## Endpoints da API

### Módulo de Produtos

Este módulo é responsável por gerenciar as informações dos produtos no sistema.

#### **Recursos:**

* **Produto:** Representa um item individual na despensa ou lista de compras.

#### **Atributos do Produto:**

* `id` (inteiro): Identificador único do produto.
* `nome` (texto): Nome do produto (obrigatório).
* `preco` (decimal, opcional): Preço unitário do produto.
* `quantidade_estoque` (inteiro, padrão: 0): Quantidade em estoque.
* `foto` (texto, opcional): URL ou caminho para a foto do produto.
* `local_compra` (texto, opcional): Local de compra do produto.
* `departamento` (texto, opcional): Departamento do produto.
* `created_at` (timestamp): Data de criação do produto.
* `updated_at` (timestamp): Data da última atualização do produto.

#### **Endpoints:**

##### **1. Listar Produtos**

* **Endpoint:** `/produtos`
* **Verbo:** `GET`
* **Descrição:** Retorna uma lista de todos os produtos cadastrados.
* **Parâmetros (Opcional para o MVP):**
    * (Ex: `?nome=Arroz`, `?departamento=Alimentos` - Filtrar por nome ou departamento)
* **Exemplo de Resposta:**

```json
[
    {
        "id": 1,
        "nome": "Arroz Tipo 1",
        "preco": 5.50,
        "quantidade_estoque": 10,
        "foto": "url_da_foto.jpg",
        "local_compra": "Supermercado X",
        "departamento": "Alimentos",
        "created_at": "2024-10-26T10:00:00.000000Z",
        "updated_at": "2024-10-26T10:00:00.000000Z"
    },
    {
        "id": 2,
        "nome": "Detergente",
        "preco": 2.80,
        "quantidade_estoque": 5,
        "foto": "url_da_foto_detergente.jpg",
        "local_compra": "Mercado Y",
        "departamento": "Limpeza",
        "created_at": "2024-10-26T10:11:00.000000Z",
        "updated_at": "2024-10-26T10:11:00.000000Z"
    },
    ...
]
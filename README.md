# Candomblé API Mobile

API backend desenvolvida em Laravel para aplicação mobile, com foco em autenticação e gerenciamento de usuários.

## Sobre o Projeto

Esta é uma API RESTful construída com Laravel 12 e PHP 8.4, projetada para servir como backend de uma aplicação mobile. O projeto utiliza arquitetura modular com DTOs, Services e Resources para manter código limpo e organizado.

## Tecnologias Principais

- **PHP**: 8.4
- **Framework**: Laravel 12
- **Autenticação**: JWT (tymon/jwt-auth)
- **Banco de Dados**: MySQL (com suporte a SQLite)
- **Queue**: Laravel Horizon
- **Cache/Session**: Redis/Predis

## Funcionalidades Implementadas

### Autenticação de Usuários

O sistema de autenticação completo foi implementado com as seguintes funcionalidades:

- **Criação de usuário**: Registro de novos usuários com validação de dados brasileiros (CPF/RG)
- **Login**: Autenticação via email e senha com geração de token JWT
- **Recuperação de senha**: Fluxo completo de recuperação de senha (planejado para implementação futura)

> **Nota**: Inicialmente foram implementadas apenas as funcionalidades de criação de usuário e login. Posteriormente, foi decidido adicionar também o fluxo completo de recuperação de senha para melhorar a experiência do usuário.

## Instalação

### Pré-requisitos

- PHP 8.4 ou superior
- Composer
- MySQL
- Redis (opcional)
- Node.js e NPM (para assets)

### Passo a Passo

1. Clone o repositório:
```bash
git clone <repository-url>
cd candomble-api-mobile
``

2. Configure o ambiente:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure as variáveis de ambiente no arquivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

JWT_SECRET=sua_chave_secreta
JWT_PUBLIC=candomblesystems
JWT_ISSUER=candomblesystems.com.br
```

4. Instale as dependências:
```bash
./up.sh
```

5. Execute as migrations:
```bash
php artisan migrate
```

## Testes

Execute os testes com:

```bash
./test.sh
```

## Padrões de Código

O projeto utiliza Laravel Pint para formatação de código:

```bash
./vendor/bin/pint
```

Configurações personalizadas estão em `pint.json`.

## Arquitetura

### Camadas da Aplicação

- **Controllers**: Recebem requisições e delegam lógica para Services
- **Requests**: Validação de dados de entrada
- **Services**: Lógica de negócio
- **DTOs**: Objetos de transferência de dados
- **Resources**: Formatação de respostas
- **Exceptions**: Tratamento customizado de erros

### Exceções Customizadas

- `BaseException`: Base para todas as exceções
- `InvalidParamException`: Parâmetros inválidos (400)
- `NotFoundException`: Recurso não encontrado (404)
- `ConflictException`: Conflito de dados (409)
- `ServerException`: Erro interno do servidor (500)

## Estrutura de Resposta Padrão

Todas as respostas da API seguem o formato:

```json
{
  "code": "S001",
  "message": "technical message",
  "userMessage": "mensagem amigável",
  "data": {}
}
```

## Segurança

- Senhas são hash usando bcrypt (12 rounds)
- Autenticação via JWT com algoritmo HS256
- Validação de documentos brasileiros (CPF/RG)
- Proteção contra SQL Injection via Eloquent ORM
- Rate limiting configurável

## Scripts Úteis

- `./up.sh`: Inicia ambiente Docker
- `./test.sh`: Executa testes em ambiente configurado

## Licença

Este projeto está sob a licença MIT.

## Suporte

Para reportar problemas ou sugerir melhorias, abra uma issue no repositório.

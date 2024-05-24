# Autentição e CRUD de estudantes

Projeto criado como teste técnico 

Funcionalidades implementadas

- Listar todos os alunos cadastrados.
- Adicionar um novo aluno
- Visualizar os detalhes de um aluno específico.
- Atualizar as informações de um aluno.
- Excluir um aluno do sistema.
- Sistema de login e autenticação

## Configuração

Após o download do projeto :

`composer update`

Após configurar as váriaveis do banco de dados no arquivo .env execute as migrations :

`php spark migrate`

## Execução 

Execute o projeto com:

`php spark serve`

# Testes

Para facilitar os testes foi adicionar a raiz do projeto um arquivo contendo as rotas no insomnia

## Versões

- PHP 8.1^
- CodeIgniter 4

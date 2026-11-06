<p align="center" style="margin-bottom: 56px;">
    <img width="400" alt="Votus Logo" src="./public/images/logo.png" />
</p>

# Gilvam Vieira - Corretor de Imóveis

## Sobre o projeto

O **Gilvam Vieira** é uma aplicação web para apoiar a gestão imobiliária. O sistema reúne cadastros de clientes e imóveis e permite criar, editar e consultar contratos, incluindo suas versões e documentos para download.

Também oferece autenticação, recuperação de senha e gerenciamento de perfil.

## Funcionalidades

- **Clientes:** cadastro e gerenciamento de clientes.
- **Imóveis:** cadastro e gerenciamento de imóveis, com busca de clientes.
- **Contratos:** criação para pessoas físicas e jurídicas, edição, consulta de versões e download de documentos.
- **Conta:** login, recuperação de senha e gerenciamento de perfil.

## Tecnologias

- PHP 8.3 ou superior
- Laravel 13
- PostgreSQL hospedado no Supabase
- Supabase Storage para imagens
- Vite, Tailwind CSS e Alpine.js
- Tiptap para edição de conteúdo
- PHPWord para documentos

As versões das dependências estão registradas em `composer.lock` e `package-lock.json`.

## Requisitos

- PHP 8.3 ou superior
- Composer
- Node.js e npm
- Git
- Projeto configurado no Supabase

## Instalação

Clone o repositório e acesse a pasta:

```bash
git clone https://github.com/NotCrazyDog-hub/gilvam-corretor.git
cd gilvam-corretor
```

Instale as dependências:

```bash
composer install
npm install
```

## Configuração do `.env`

Crie o arquivo `.env` copiando o modelo:

```bash
cp .env.example .env
```

Abra o `.env` e preencha as configurações da aplicação e do Supabase. Use os dados de conexão PostgreSQL e as credenciais exibidas no painel do seu projeto Supabase:

```dotenv
APP_NAME=Gilvam
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=pt_BR

DB_CONNECTION=pgsql
DB_HOST=<host-do-supabase>
DB_PORT=<porta-do-supabase>
DB_DATABASE=postgres
DB_USERNAME=<usuario-do-supabase>
DB_PASSWORD=<senha-do-banco>

IMAGE_STORAGE_DISK=supabase
SUPABASE_URL=https://<id-do-projeto>.supabase.co
SUPABASE_SECRET_KEY=<chave-secreta-do-projeto>
SUPABASE_STORAGE_BUCKET=<nome-do-bucket>

MAIL_MAILER=log
```

Substitua os valores entre `< >` pelos dados reais do projeto. Para encontrar os dados do banco, abra **Connect** no painel do Supabase. Para as imagens, crie um bucket privado no Supabase Storage e informe seu nome em `SUPABASE_STORAGE_BUCKET`.

Não use a porta `3306`, que é padrão do MySQL. Use a porta indicada pelo Supabase para o método de conexão escolhido.

Não compartilhe o `.env` nem envie esse arquivo ao repositório. A chave secreta do Supabase deve permanecer no servidor.

### Gerar a chave da aplicação

Depois de salvar o `.env`, gere a chave do Laravel:

```bash
php artisan key:generate
```

Esse comando preencherá `APP_KEY` no arquivo `.env`.

### Criar as tabelas

Com o `.env` preenchido e `APP_KEY` gerada, execute as migrations no banco PostgreSQL do Supabase:

```bash
php artisan migrate
```

## Desenvolvimento local

Inicie os processos de desenvolvimento configurados pelo projeto:

```bash
composer run dev
```

A aplicação fica disponível, por padrão, em:

```text
http://localhost:8000
```

Também é possível iniciar o Laravel e o Vite em terminais separados:

```bash
php artisan serve
```

```bash
npm run dev
```

Para gerar os arquivos finais do frontend:

```bash
npm run build
```

---

<p align="center">
    Equipe de Desenvolvimento do Gilvam Vieira
</p>
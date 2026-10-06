# CCMPT - Centro Cultural e Memorial Padre Tullio

Este repositório contém o código-fonte do site institucional e do sistema de Backoffice (gestão de conteúdo) do **Centro Cultural e Memorial Padre Tullio (CCMPT)**. O projeto foi desenvolvido como um Trabalho de Conclusão de Curso (TCC).

O sistema possui uma arquitetura moderna e separada (Decoupled), com o frontend construído em **Vue.js** (SPA) e o backend servindo uma **API REST em PHP puro** conectada a um banco de dados MySQL.

## 🚀 Tecnologias Utilizadas

### Frontend (Aplicação SPA)
* **Vue.js 3** (Composition API & `<script setup>`)
* **Vite** (Build tool super rápida)
* **Vue Router** (Roteamento de páginas)
* **Pinia** (Gerenciamento de estado)
* **Vue Quill** (Editor de texto rico - *Rich Text*)
* CSS Nativo / Variáveis CSS (Apresentando um design limpo e responsivo)

### Backend (API RESTful)
* **PHP 8.2** (Arquitetura MVC personalizada e leve)
* **MySQL / MariaDB** (Banco de dados relacional)
* **Intervention Image** (Processamento de imagens, recortes e otimização para WebP)
* **JWT (JSON Web Tokens)** (Autenticação segura de API)

---

## ⚙️ Funcionalidades

### Site Público (Frontend)
* **Páginas Institucionais Dinâmicas:** Exibição de páginas criadas através do painel.
* **Mural de Notícias:** Listagem e leitura de posts/notícias com imagens de capa.
* **Acervo Fotográfico:** Galeria de imagens otimizadas em WebP com visualização em Lightbox.
* **Contato:** Formulário público de contato integrado direto com o painel de administração.
* **Acervo Histórico:** (Em desenvolvimento)

### Painel Administrativo (Backoffice)
* Protegido por Login/Senha e Tokens JWT.
* **Gestão de Usuários:** Cadastro, edição e exclusão de usuários e administradores.
* **Gestão de Páginas:** Editor de texto estilo Word para criar páginas fixas no site.
* **Gestão de Notícias:** Cadastro de postagens com capa e conteúdo formatado.
* **Arquivo Fotográfico:** Criação de álbuns e upload múltiplo de fotos com processamento inteligente (geração de miniatura automática e conversão para WebP).
* **Caixa de Mensagens:** Recebimento e gerenciamento das mensagens enviadas pelo formulário de contato do site público.
* **Acervo Histórico:** Gestão das peças e itens históricos do memorial.

---

## 🛠️ Como rodar o projeto localmente

Como o projeto é dividido em duas partes, você precisará rodar dois servidores simultaneamente.

### Pré-requisitos
* PHP 8.2+ com extensão **GD**, **PDO** e **mbstring** habilitadas no `php.ini`.
* Node.js v18+ e NPM.
* MySQL/MariaDB rodando (ex: através do XAMPP).

### 1. Configurando o Banco de Dados
1. Crie um banco de dados no MySQL chamado `ccmpt`.
2. Importe o arquivo `ccmpt.sql` (ou rode as *migrations*/*seeds* através do `api/alter.php` e `api/seed.php`).
3. Opcional: Se precisar alterar senhas do banco de dados, edite o arquivo de configuração de banco localizado na pasta do core da API.

### 2. Rodando o Backend (API PHP)
Abra um terminal na raiz do projeto e inicie o servidor embutido do PHP apontando para a pasta pública da API:
```bash
php -S localhost:8000 -t api/public
```

> **Nota:** Certifique-se de que o seu `php.ini` possui a linha `extension=gd` descomentada para que o envio e processamento das imagens funcione perfeitamente.

### 3. Rodando o Frontend (Vue.js)
Abra **outro** terminal, navegue para a pasta `frontend` e instale as dependências. Depois, rode o servidor de desenvolvimento:
```bash
cd frontend
npm install
npm run dev
```
O Vite iniciará o frontend e informará uma URL (geralmente `http://localhost:5173/`).

---

## 🔗 Estrutura de Pastas

```text
/
├── api/                   # Backend API (PHP)
│   ├── controllers/       # Controladores da API
│   ├── core/              # Classes base (Router, Database, ImageService, JWT, etc)
│   ├── models/            # Modelos de comunicação com o banco
│   ├── public/            # Ponto de entrada (index.php) e pasta de uploads
│   └── vendor/            # Dependências PHP (Composer)
│
├── frontend/              # Frontend Application (Vue.js)
│   ├── public/            # Ícones e arquivos estáticos
│   └── src/
│       ├── assets/        # CSS global e imagens base
│       ├── components/    # Componentes reutilizáveis (Editor, Cartões)
│       ├── layouts/       # Estruturas de layout (AdminLayout, PublicLayout)
│       ├── router/        # Arquivo de rotas e Navigation Guards
│       ├── stores/        # Arquivos de estado global (Pinia)
│       └── views/         # Páginas e Telas (divididas em /admin e /public)
│
└── ccmpt.sql              # Dump inicial do banco de dados
```

## 👨‍💻 Autor
Hian Netto - Projeto de Trabalho de Conclusão de Curso (TCC).

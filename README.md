# Sistema de Cadastro de Produtos

Sistema de cadastro de produtos desenvolvido com Laravel (back-end) e Vue.js (front-end).

## Requisitos

- PHP 8.1+
- Node.js 18+
- Composer
- MySQL ou SQLite

## Instalação

### Backend (Laravel)

1. Entre na pasta do backend:
```bash
cd backend
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o arquivo .env:
```bash
cp .env.example .env
```

4. Gere a chave da aplicação:
```bash
php artisan key:generate
```

5. Execute as migrações:
```bash
php artisan migrate
```

6. Inicie o servidor:
```bash
php artisan serve
```

### Frontend (Vue.js)

1. Entre na pasta do frontend:
```bash
cd frontend
```

2. Instale as dependências:
```bash
npm install
```

3. Inicie o servidor de desenvolvimento:
```bash
npm run dev
```

## Documentação da API

A documentação da API está disponível em:
```
http://localhost:8000/api/documentation
```

## Tecnologias Utilizadas

- Laravel 10+
- Vue.js 3
- Axios
- Swagger/OpenAPI
- MySQL/SQLite
- Formik + Yup
- Vue Toastification

## Funcionalidades

### Frontend
- Lista de cards com scroll infinito e paginação
- Componentes reutilizáveis para CRUD
- Notificações toast para feedback
- Loading states com skeleton
- Validação de formulários com Formik e Yup

### Backend
- API RESTful para produtos (GET, POST, PUT, DELETE)
- Documentação completa com Swagger
- Paginação na listagem de produtos
- Validação de dados
- Mensagens de sucesso e erro
- Tema visual personalizado (amarelo e preto) 
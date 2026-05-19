# Pro Academic Hub

Sistema web desenvolvido para gerenciamento e organização de projetos acadêmicos, criado como Trabalho de Graduação (TG) do curso de Análise e Desenvolvimento de Sistemas.

---

## 📌 Sobre o Projeto

O **Pro Academic Hub** é uma plataforma web voltada para o gerenciamento acadêmico de projetos, tarefas e comunicação entre alunos, coordenadores, professores e administradores.

O sistema foi desenvolvido com foco em organização, produtividade e centralização de informações acadêmicas, permitindo o acompanhamento completo de projetos e atividades dentro do ambiente educacional.

---

## 🚀 Funcionalidades

### 👤 Usuários e Permissões
- Cadastro de usuários
- Sistema de login e autenticação
- Controle de acesso por níveis:
  - Administrador
  - Coordenador
  - Professor
  - Aluno

### 📂 Gerenciamento de Projetos
- Cadastro de projetos
- Edição de projetos
- Visualização detalhada
- Arquivamento de projetos
- Atualização de status e prioridade

### ✅ Gerenciamento de Tarefas
- Cadastro de tarefas
- Edição de tarefas
- Controle de status
- Definição de prioridade
- Visualização de tarefas por projeto

### 💬 Comunicação e Suporte
- Sistema de comentários
- Chamados de suporte
- Respostas administrativas
- Relatórios e acompanhamento

### 📅 Organização Acadêmica
- Calendário acadêmico
- Gerenciamento de horários
- Visualização de atividades

### 👤 Perfil do Usuário
- Upload de foto
- Edição de perfil
- Informações do usuário autenticado

---

## 🛠️ Tecnologias Utilizadas

### Front-end
- HTML5
- CSS3
- JavaScript
- React

### Back-end
- PHP

### Banco de Dados
- MySQL

### Ferramentas
- XAMPP
- MySQL Workbench
- GitHub

---

## 📁 Estrutura do Projeto

```bash
ProAcademicHub/
│
├── Assets/
│   ├── Css/
│   ├── Img/
│   ├── Js/
│   └── Uploads/
│
├── Config/
├── Includes/
├── Public/
├── Shared/
├── Users/
│   ├── Admin/
│   ├── Coordinator/
│   ├── Students/
│   └── Teachers/
│
├── innovatech_db.sql
├── README.md
└── .gitignore
```

---

## ⚙️ Como Executar o Projeto

### 1️⃣ Clone o repositório

```bash
git clone URL_DO_REPOSITORIO
```

### 2️⃣ Mova o projeto para a pasta do XAMPP

Coloque a pasta do projeto dentro do diretório:

```bash
htdocs
```

### 3️⃣ Inicie os serviços no XAMPP

- Apache
- MySQL

---

## 🗄️ Configuração do Banco de Dados

### 1️⃣ Crie o banco de dados

Abra o **phpMyAdmin** ou **MySQL Workbench** e crie um banco chamado:

```sql
innovatech_db
```

### 2️⃣ Importe a estrutura do banco

Importe o arquivo:

```bash
innovatech_db.sql
```

> ⚠️ O arquivo `innovatech_db.sql` contém apenas a estrutura do banco de dados, sem informações reais de usuários ou projetos.

### 3️⃣ Configure as credenciais

Verifique as credenciais no arquivo:

```bash
Config/db.php
```

Exemplo:

```php
$host = "localhost";
$dbname = "innovatech_db";
$user = "root";
$pass = "";
```

---

## ▶️ Executando o Sistema

Acesse no navegador:

```bash
http://localhost/ProAcademicHub/Public
```

---

## 🎯 Objetivo Acadêmico

Este projeto foi desenvolvido como Trabalho de Graduação, com foco em aplicar conceitos de:

- Desenvolvimento Web
- Banco de Dados
- Programação Back-end
- Controle de Permissões
- Integração Front-end e Back-end
- Estruturação de sistemas web

---

## 📌 Status do Projeto

🚧 Projeto em desenvolvimento.

---

## 👩‍💻 Desenvolvido por

Suélen Barboza dos Santos  
Estudante de Análise e Desenvolvimento de Sistemas — FATEC

### 🔗 Contato

GitHub: https://github.com/seuusuario  
LinkedIn: https://linkedin.com/in/suelenbarboza

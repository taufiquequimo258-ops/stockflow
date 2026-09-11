# StockFlow Enterprise — Sistema de Gestão de Inventário e Requisições

> **Disciplina:** Desenvolvimento de Aplicativos Web Empresariais  
> **Autor:** [SEU NOME COMPLETO]  
> **Estado do Projeto:** Concluído / 100% Funcional  

---

## 📌 1. Descrição do Projeto

O **StockFlow Enterprise** é um sistema Web empresarial desenvolvido para otimizar, automatizar e controlar a gestão de produtos, categorias, movimentos de stock e requisições de materiais em pequenas e médias organizações.

O sistema resolve o problema da desorganização na alocação de insumos, eliminando controlos informais em papel e planilhas, oferecendo controlo de acesso baseado em perfis (RBAC), alertas automáticos de stock mínimo e histórico completo de requisições.

---

## 🚀 2. Funcionalidades Principais

* 🔐 **Autenticação & Segurança:** Login com palavra-passe encriptada (`bcrypt`), renovação de sessão, proteção contra CSRF via tokens em todos os formulários e sanitização contra XSS.
* 👥 **Controlo de Acessos por Perfis (RBAC):**
  * **Administrador:** Acesso total (Gestão de Utilizadores, Categorias, Produtos, Requisições e Dashboard Geral).
  * **Operador:** Gestão de Produtos, Categorias e Aprovação/Baixa de Requisições.
  * **Requisitante:** Criação e acompanhamento do estado das suas próprias requisições.
* 📦 **Gestão de Produtos (CRUD Completo):** Registo com código SKU único, nome, categoria, preço unitário, quantidade em stock e limiar de stock mínimo.
* 🏷️ **Gestão de Categorias (CRUD Completo):** Organização e categorização lógica dos itens do inventário.
* 📋 **Gestão de Requisições & Movimentos:** Solicitação de saídas de materiais ou reposição de stock com validação automática de disponibilidade.
* 📊 **Dashboard Dinâmico:** Cartões com indicadores estatísticos, gráfico/tabela de produtos em alerta de stock e requisições recentes.
* 📱 **Interface 100% Responsiva:** Desenvolvida em Bootstrap 5, compatível com computadores, tablets e smartphones.

---

## 🛠️ 3. Stack Tecnológica

* **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3, FontAwesome 6 Icons.
* **Backend:** PHP 8.2+ (Arquitetura MVC limpa com Front Controller e Prepared Statements PDO).
* **Base de Dados:** MySQL 8.0 / MariaDB (Modelo relacional 3FN com chaves estrangeiras e integridade referencial).
* **Controlo de Versões:** Git & GitHub.

---

## 🔑 4. Credenciais de Teste

Para efeitos de avaliação e demonstração do sistema, utilize as seguintes contas pré-cadastradas:

| Perfil | E-mail | Palavra-passe | Nível de Permissão |
| --- | --- | --- | --- |
| **Administrador** | `admin@stockflow.com` | `password123` | Acesso Total ao Sistema |
| **Operador** | `operador@stockflow.com` | `password123` | Gestão de Stock e Aprovações |
| **Requisitante** | `maria@empresa.com` | `password123` | Solicitação de Materiais |

---

## ⚙️ 5. Guia de Instalação e Execução

### Pré-requisitos:
* Servidor Web local (XAMPP, WAMP, LARAGON ou PHP 8.2+ CLI).
* Servidor MySQL / MariaDB.

### Passo 1: Clonar ou Extrair o Repositório
```bash
git clone https://github.com/SEU_UTILIZADOR/stockflow.git
cd stockflow
```

### Passo 2: Configurar a Base de Dados MySQL
1. Abra o MySQL Workbench, phpMyAdmin ou linha de comandos do MySQL.
2. Importe o ficheiro `database/schema.sql`:
```sql
SOURCE database/schema.sql;
```
*(Nota: O script cria a base de dados `stockflow_db`, todas as tabelas e insere os dados iniciais de teste)*.

### Passo 3: Configurar a Ligação no PHP
Caso a sua palavra-passe do MySQL não seja vazia (`""`), edite o ficheiro `config/database.php`:
```php
return [
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'stockflow_db',
    'username' => 'root',
    'password' => 'SUA_SENHA_AQUI',
    'charset' => 'utf8mb4'
];
```

### Passo 4: Executar a Aplicação
Abra o terminal na pasta do projeto e inicie o servidor embutido do PHP:
```bash
php -S localhost:8000 -t public
```

Aceda no navegador ao endereço:  
👉 **http://localhost:8000**

---

## 📄 6. Repositório & Apresentação em Vídeo

* **Link do Repositório GitHub:** `[INSERIR LINK DO GITHUB]`
* **Link do Vídeo de Demonstração (YouTube/Drive):** `[INSERIR LINK DO VÍDEO]`

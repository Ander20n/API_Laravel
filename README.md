# API_Laravel
API RESTful utilizando Laravel para gerenciar um sistema de  gerenciamento de projetos e tarefas

Para configurar e rodar o projeto Laravel com Passport e MySQL, siga as instruções a seguir:

**Pré-requisitos:**

- PHP >= 7.4
- Composer
- MySQL
- Git

Configuração e Execução

**Passos Gerais**

**1. Clonar o Repositório:**
 </br></br>git clone https://github.com/Ander20n/API_Laravel.git
 </br>Depois abrir o repositório no local onde você salvou no seu computador

**2. Instalar Dependências:**
   </br></br>composer install

**3. Configurar o Ambiente:**

   - Crie um banco de dados MySQL para o projeto.
   - Faça uma cópia do arquivo .env.example e renomeie para .env.
   - Configure as variáveis de ambiente em .env, especialmente as relacionadas ao banco de dados e ao Passport.

**4. Configurar Passport:**
   </br></br>php artisan passport:install

**5. Executar Migrações e Seeders:**
   </br></br>php artisan migrate --seed

**6. Gerar Chave de Aplicação:**
   </br></br>php artisan key:generate

**7. Gerar Documentação Swagger:**

   - Instale o Swagger se ainda não estiver instalado:
     composer require darkaonline/l5-swagger

   - Publique os arquivos de configuração do Swagger:
     php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

   - Acesse a documentação Swagger em http://seu-domínio/api/documentation.

**8. Executar o Servidor de Desenvolvimento:**
   </br></br>php artisan serve

**Instruções por Sistema Operacional**

- Windows: Utilize o prompt de comando ou o PowerShell.
- Linux/MacOS: Utilize o terminal.

**Notas Adicionais**

- Certifique-se de que o MySQL está em execução e acessível.
- Ajuste as permissões de arquivo conforme necessário para o ambiente de produção.

Qualquer dúvida: https://www.linkedin.com/in/ander20n/

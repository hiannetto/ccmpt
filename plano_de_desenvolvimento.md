# Planejamento de Desenvolvimento

**Centro Cultural e Memorial Padre Tiago | Plataforma Web e CMS**

Este cronograma detalha as etapas de desenvolvimento do site institucional e do sistema de gerenciamento. O projeto foi dividido em 6 fases lógicas, indo desde a modelagem dos dados até a entrega final.

### Fase 1: Arquitetura e Modelagem de Dados

*(Duração estimada: Semana 1)*

* Modelagem do banco de dados relacional (MariaDB) para armazenar as notícias, eventos, acervo, categorias, contatos e a nova estrutura de Galerias Fotográficas.
* Definição das entidades e relacionamentos (ex: ligação entre os artigos do memorial e as categorias de filtro, e fotos vinculadas às galerias).
* Configuração do ambiente de desenvolvimento local e dos repositórios de código.
* Estruturação das regras de negócio gerais (como a regra de exclusão lógica para não perder os dados do acervo e fotos).

### Fase 2: Desenvolvimento da API (Backend)

*(Duração estimada: Semanas 2 e 3)*

* Criação da API RESTful (em PHP) responsável por processar as informações.
* Implementação do sistema de autenticação e rotas protegidas de segurança.
* Desenvolvimento das funções de Cadastro, Edição, Leitura e Inativação para as Notícias, Eventos, Acervo, Galerias Fotográficas e Páginas Estáticas.
* Criação do recebimento automático de mensagens do formulário de contato do site.

### Fase 3: Armazenamento Local e Otimização de Imagens

*(Duração estimada: Semana 4)*

* Criação da rotina de upload de arquivos para o próprio servidor da aplicação.
* Implementação do motor de processamento de imagens (via backend) para aplicar o redimensionamento de limite máximo (ex: 1920px) e a conversão automatizada para o formato otimizado WebP.
* Geração de miniaturas (thumbnails) de baixo peso (10kb a 20kb) no momento do upload para uso nas listagens e na amostragem do site.
* Vinculação dos caminhos dos arquivos físicos locais com os cadastros dentro do banco de dados.

### Fase 4: Desenvolvimento do Backoffice (Painel Administrativo)

*(Duração estimada: Semanas 5 e 6)*

* Estruturação do painel administrativo como uma aplicação dinâmica (Vue.js).
* Criação da tela de login e controle de níveis de acesso da equipe (Administrador / Editor).
* Desenvolvimento das telas para gerenciar o conteúdo: listagens, formulários e filtros de busca internos.
* Criação da interface otimizada de upload de fotos, permitindo a criação de novas Galerias/Categorias diretamente na mesma tela (sem recarregar ou mudar de página).
* Criação da interface de triagem de contatos (controle visual de "Pendente" ou "Atendido").

### Fase 5: Desenvolvimento do Site Institucional (Frontend)

*(Duração estimada: Semanas 7 e 8)*

* Desenvolvimento da interface pública visual, com design responsivo para funcionar bem em celulares e computadores (Vue.js).
* Construção das páginas institucionais com a história e áreas de atuação.
* Implementação das vitrines separadas: o Acervo Histórico (para os 400 itens físicos catalogados com filtros) e o Arquivo Fotográfico (focado nos álbuns/galerias das 5.000 fotos do Padre Tiago).
* Aplicação de Lazy Loading (carregamento sob demanda) e rolagem inteligente nas galerias para exibir as fotos gradativamente, poupando a banda do servidor.
* Integração do formulário de contato do site com o painel da equipe.
* Aplicação das boas práticas de SEO.

### Fase 6: Testes, Implantação e Treinamento

*(Duração estimada: Semana 9)*

* Bateria de testes de usabilidade, responsividade, envio em lote de imagens e correção final de bugs (Homologação).
* Configuração do servidor de produção, instalação do banco de dados e implantação (deploy) da plataforma.
* Reunião de treinamento com a equipe do Centro Cultural para ensinar a utilizar o painel de gerenciamento (especialmente o fluxo dinâmico de upload e criação de galerias).
* Entrega oficial da plataforma em pleno funcionamento.
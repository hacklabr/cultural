Cultural
========

A WordPress magazine theme ready to connect with the [Mapas Culturais API](https://github.com/hacklabr/mapasculturais)

## Guia de instalação, configuração e utilização

### Instalação
1. Faça download da última versão disponível do tema na [página de releases](https://github.com/hacklabr/cultural/releases);
2. Descompacte o arquivo baixado na pasta **wp-contentent/themes/** de seu site wordpress;
3. Logado como administrador no seu site wordpress, entre na página **Temas** (_em inglês **Themes**_) do menu **Aparência** (_em inglês **Appearence**_) e ative o tema **Cultural**;
4. Entre na página do menu **Mapas Culturais** e informe os campos **URL da instalação do Mapas Culturais** (_exemplo **http://spcultura.prefeitura.sp.gov.br/** ou **http://mapa.cultura.rs.gov.br/**_) e **Nome da instalação do Mapas Culturais** (_exemplo **SP Cultura**, ou **Mapa da Cultura**_).

### Configuração

#### Filtros de eventos
A configuração de quais eventos serão "puxados" da plataforma Mapas Culturais é feita na página do menu **Mapas Culturais** e ela aparece automaticamente após a configuração da URL e do Nome da instalação do Mapas Culturais (_item 4 da seção [Instalação](#Instalação)_);

#### Categorias
Ao Criar uma categoria você pode configurar uma cor, que será utilizada em diversas partes do site, e informar se esta categoria terá, ou não, uma agenda de eventos relacionada. Para categorias com agenda de eventos um formulário será exibido para a configuração dos filtros a serem utilizados para a agenda desta categoria;

#### Menus
A configuração dos menus do site é feita na página **Menus** do menu **Aparência** (_em inglês **Appearence**_).
Crie 4 menus e associe as posições abaixo: 
- **Menu primário** - _Utilize este menu para colocar os links para as categorias. Este menu é sensível às cores configuradas nas categorias_;
- **Menu secundário** - _Fica abaixo do menu principal, com um estilo mais discreto. Utilize este menu para páginas de expediente, contato, etc_;
- **Menu Mobile** - _Usado para configurar o menu do site na versão mobile_;
- **Régua de Marcas** - _Usado para configurar a régua de marcas do rodapé do site. Ver seção [Régua de marcas](#Régua de marcas)_


#### Régua de marcas
A configuração da régua de marcas é feita em duas etapas: cadastro das marcas e configuração da régua de marcas.

Para  cadastrar uma marca entre na página **Adicionar Nova** do menu **Régua de Marcas**. Você deve informar o nome da marca (_exemplo **SP Cultura**_), URL do site da marca (_exemplo **http://spcultura.prefeitura.sp.gov.br**_) e enviar através da caixa **imagem destacada**, o arquivo da imagem do logo/marca no formato _jpeg_ ou _png_.

Após cadastrar as marcas você deve configurar a régua. Para isto entre na página **Menus** do menu **Aparência** (_em inglês **Appearence**_) e selecione o menu **Régua de Marcas**, criado anteriormente. Certifique-se de que a opção **Régua de Marcas** está selecionada nas **opções de tela**.

No primeiro nível do menu você deve colocar as seções (_exemplos: **Patrocinadores**, **Organizadores**, **Realização**, etc_) utilizando links e no segundo nível as marcas.

#### Destaques da Home e das Categorias
Para destacar ou fixar posts nos começos das listagens da home e das categorias cole as urls dos posts desejados nas caixas da página do menu **Post Highlights**

### Posts/Páginas
#### Relacionando post/página com entidade do Mapas Culturais
Relacionar uma entidade do Mapas Culturais a um post de seu site você faz com que, em diversos lugares do site, as urls dos links para esta entidade do Mapas Culturais seja substituido pela url do post relacionado. Por exemplo, se você relacionar um post Y a um evento X e este evento X aparecer no resultado de uma busca por eventos, o link de **mais imformações** levará ao post Y ao invés de levar para a página do evento X no Mapas Culturais.

Além das mudanças de urls dos links pelo site, se a entidade que está sendo relacionada ao post for um evento, uma caixa de serviço do evento será exibida ao final do post.

Para relacionar um post a uma entidade do Mapas Culturais copie a url da entidade (_exemplos: **http://spcultura.prefeitura.sp.gov.br/espaco/0000/** ou **http://spcultura.prefeitura.sp.gov.br/evento/0000/**) e cole na caixa **Linkar com entidade do Mapas Culturais**.

#### Importando imagens das entidades do Mapas Culturais
Você pode importar as imagens de uma entidade e anexá-las ao post clicando no botão **Importar imagens da plataforma**

#### Caixas de serviço de eventos
Você pode adicionar caixas de serviço de eventos utilizando o shortcode **[evento ]** em qualquer ponto do conteúdo do post/página. Exemplo **[evento http://spcultura.prefeitura.sp.gov.br/evento/00000/]**

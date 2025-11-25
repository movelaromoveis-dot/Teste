# WP Product AI Summaries

Plugin intuitivo para gerar resumos HTML de produtos WooCommerce usando uma API de IA (ex: OpenAI).

**Versão:** 0.3 | **Criador:** Richard | **Desenvolvido por:** Automate AI

## Novidades v0.3

- ✨ **HUB Page**: Dashboard visual para gerenciar o plugin a partir do menu principal
- 🎨 **Template Editor**: Customize o formato HTML dos resumos diretamente no painel de configurações
- 🔗 **Action Links**: Acesso rápido às configurações ("Configurações") na página de plugins
- 📝 **Templates Predefinidos**: Seleção entre templates padrão, profissional e minimalista

## Instalação

1. Copie a pasta `wp-product-ai-summaries` para `wp-content/plugins/` do seu WordPress.
2. Ative o plugin em **Plugins → Plugins instalados**.
3. Clique no link **"Configurações"** abaixo do nome do plugin, OU vá em **Settings → AI Summaries**.
4. Configure sua chave de API (ex: OpenAI).

## Configuração

### Aba Configurações da IA

1. **API Key**: Cole sua chave de API do OpenAI (obtenha em https://platform.openai.com/api-keys)
2. **Modelo (opcional)**: Deixe em branco para usar `gpt-4o-mini` (padrão)
3. **Inserir em**: Escolha onde inserir o resumo
	- **Descrição (content)**: Insere no corpo principal do produto
	- **Resumo (excerpt)**: Insere no campo de resumo/descrição curta
	- **Ambos (both)**: Insere em ambos os campos
4. **Gerar resumo breve**: Marque para gerar além do HTML, um texto simples para o excerpt

### Aba Templates HTML

1. **Selecionar Template**: Escolha entre os templates predefinidos ou personalize
2. **Editor de Template**: Customize o HTML conforme suas necessidades
	- Use `{CONTEUDO}` como placeholder onde o resumo deve ir
	- Suporta CSS dentro de `<style>`
	- Apenas tags seguras são permitidas

## Uso

### Via HUB Page (recomendado)

1. Clique em **"WP Product AI"** no menu lateral do WordPress
2. Visualize o dashboard com links rápidos
3. Clique em **"Abrir Configurações"** para ajustar template e API

### Via Metabox de Produto

1. Edite um produto WooCommerce
2. No painel lateral, localize o metabox **"⚡ AI Summary"**
3. Clique em **"✨ Gerar Resumo IA"** para solicitar um resumo automático
4. O HTML será gerado e exibido no textarea com feedback visual em tempo real.
5. Clique em **"✓ Inserir no Resumo"** para salvar (com auto-save automático)
6. O produto será salvo automaticamente

## Interface

- **HUB Page**: Design gradiente com cards informativos para acesso rápido
- **Página de Settings**: Seções organizadas (Configurações da IA + Templates HTML)
- **Metabox de Produto**: Layout intuitivo com status messages, animações e feedback
- **Admin Script**: Validações, spinners de carregamento e mensagens de sucesso/erro

## Segurança

- A chave de API é salva nas opções do WordPress. Proteja seu site!
- O plugin faz requisições HTTPS para `https://api.openai.com/v1/chat/completions`
- HTML retornado é filtrado com `wp_kses()` para permitir apenas tags seguras
- Nonces protegem todas as requisições AJAX
- Verificações de capacidade (`current_user_can`) controlam acesso

## Personalizações Possíveis

- Edite o HTML no **Editor de Template** para ajustar cores, fontes, estrutura
- Use `{TITULO}` e `{CONTEUDO}` como placeholders
- Adicione classes CSS customizadas
- Escolha entre templates predefinidos ou crie o seu próprio

## Melhorias Futuras

- Extrair atributos do produto (preço, SKU, categorias) automaticamente
- Histórico de resumos gerados
- Controle de tom/linguagem na geração
- Suporte a múltiplas IA (Claude, Llama, etc.)
- Cache de resumos

## Observações

- Teste em ambiente de staging antes de usar em produção
- Certifique-se de ter créditos suficientes na API de IA
- A geração é feita sob demanda (não é automática)
- Auto-save usa Gutenberg quando disponível, com fallback para AJAX no editor clássico


# WP Product AI Summaries

Plugin intuitivo para gerar resumos HTML de produtos WooCommerce usando uma API de IA (ex: OpenAI).

**Versão:** 0.6.1-beta | **Criador:** Richard & Automate AI

## Novidades v0.6.1-beta (26/11/2025) - Security Audit & Code Cleanup

### 🔒 Segurança Aprimorada
- **Sanitização de Input**: `wp_unslash()` adicionado ao acesso de `$_GET['tab']`
- **Validação de Abas**: Whitelist de abas permitidas na HUB page
- **Sanitização de Settings**: Callback customizado para sanitizar todas as opções
- **Verificação de Permissões**: `current_user_can('manage_options')` na HUB page
- **Constantes Centralizadas**: Nonces e slugs centralizados para melhor manutenção

### 🧹 Limpeza de Código
- **Constantes Adicionadas**: `WPAI_PLUGIN_SLUG`, `WPAI_NONCE_ACTION`, `WPAI_NONCE_NAME`
- **Documentação**: URL do plugin adicionada ao header
- **License**: Tag GPL v2 or later adicionada

## Novidades v0.6 (26/11/2025) - Enhanced HUB with Tutorial & Template Preview

### ✨ Principais Melhorias
- **HUB Redesenhado com Sistema de Abas**
  - Dashboard: Visão geral com cards de acesso rápido
  - Templates: Painel de visualização com preview em tempo real
  - Tutorial: Guia intuitivo integrado com 4 seções
- **Painel de Visualização de Templates**
  - Preview ao vivo com conteúdo de exemplo
  - Atualização em tempo real ao customizar
- **Tutorial Integrado**
  - Setup: Instruções passo-a-passo
  - Configuração Recomendada: Melhor setup OpenAI
  - Editando Produtos: Como usar metabox
  - Customização de Templates: Guia HTML/CSS
  - FAQ: Respostas comuns

## Novidades v0.4 (26/11/2025) - Preview & Guide

### ✨ Novas Funcionalidades
- **Template Preview Modal**: Visualizar template antes de gerar resumo com dados de exemplo
- **Interactive HTML/CSS Guide**: Modal com guia prático explicando tags HTML suportadas
- **Live Preview Buttons**: Botões "👁 Preview" e "❓ Ajuda HTML/CSS" nas configurações
- **Result Preview**: Botão "Preview Resultado" no metabox para visualizar HTML gerado

## Novidades v0.3 (25/11/2025) - Hub & Template

### ✨ Novas Funcionalidades
- **HUB Page**: Dashboard no menu principal do WordPress para gerenciamento centralizado
- **Plugin Action Links**: Link "Configurações" direto na página de plugins
- **Template Editor**: Aba de templates no painel de configurações para customização HTML
- **Templates Predefinidos**: Seleção entre templates (Padrão, Profissional, Minimalista, Personalizado)

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


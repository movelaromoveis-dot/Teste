# CHANGELOG

## v0.6.3-beta (27/11/2025) - Dynamic Tabs Navigation

### ✨ Melhorias na Interface
- **Navegação de Abas Dinâmica**: Substituição de links hardcoded por loop dinâmico usando `apply_filters('wpai_hub_tabs', array(...))`, melhorando a extensibilidade e manutenção da HUB page.

---

## v0.6.1-beta (26/11/2025) - Security Audit & Code Cleanup

### 🔒 Segurança Aprimorada
- **Sanitização de Input**: `wp_unslash()` adicionado ao acesso de `$_GET['tab']`
- **Validação de Abas**: Whitelist de abas permitidas na HUB page
- **Sanitização de Settings**: Callback customizado para sanitizar todas as opções
  - API Key: `sanitize_text_field()`
  - Modelo: `sanitize_text_field()`
  - Insert Target: validação de valores permitidos
  - Template Custom: `wp_kses_post()`
- **Verificação de Permissões**: `current_user_can('manage_options')` na HUB page com `wp_die()` fallback
- **Nonce Constants**: Constantes centralizadas para `WPAI_NONCE_ACTION` e `WPAI_NONCE_NAME`

### 🧹 Limpeza de Código
- **Constantes Adicionadas**: `WPAI_PLUGIN_SLUG`, `WPAI_NONCE_ACTION`, `WPAI_NONCE_NAME` para melhor manutenção
- **Documentação**: URL do plugin adicionada ao header
- **License**: Tag GPL v2 or later adicionada ao header

### 🐛 Correções
- Erros de acesso a `$_GET` com `wp_unslash()` 
- Erros de PowerShell relatados durante update checks
- Melhor tratamento de erros em functions remotas

### 📋 Notas Beta
- Versão de teste para validar todas as melhorias de segurança
- Feedback: Testar em ambiente local antes de produção
- Relatório de bugs via GitHub Issues

---

## v0.6 (26/11/2025) - Enhanced HUB with Tutorial & Template Preview

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

---

## v0.4 (26/11/2025) - Preview & Guide

### ✨ Novas Funcionalidades
- **Template Preview Modal**: Visualizar template antes de gerar resumo com dados de exemplo
- **Interactive HTML/CSS Guide**: Modal com guia prático explicando:
  - Cada tag HTML suportada e sua função
  - Propriedades CSS mais úteis com exemplos
  - Template de exemplo pronto para copiar/colar
  - Tags proibidas (segurança)
- **Live Preview Buttons**: Botões "👁 Preview" e "❓ Ajuda HTML/CSS" nas configurações
- **Result Preview**: Botão "Preview Resultado" no metabox para visualizar HTML gerado

### 🎨 UI/UX
- Modal overlay com animações suaves (slideIn)
- Scrollbar customizado nas modals
- Preview frame com background destacado
- Guia com código-exemplo formatado
- Botões com gradiente (667eea → 764ba2)

### 📁 Novos Arquivos
- `admin-preview.css` (2.8 KB) - Estilos das modals e guia interativo

### 🔧 Mudanças Técnicas
- Novo arquivo CSS enfileirado em settings page e product edit page
- Versão do plugin: 0.3 → 0.4
- JavaScript modularizado com funções separadas para preview/guide
- Suporte a {CONTEUDO} placeholder em template preview

---

## v0.3 (25/11/2025) Hub & Template

### ✨ Novas Funcionalidades
- **HUB Page**: Dashboard no menu principal do WordPress para gerenciamento centralizado
- **Plugin Action Links**: Link "Configurações" direto na página de plugins
- **Template Editor**: Aba de templates no painel de configurações para customização HTML
- **Templates Predefinidos**: Seleção entre templates (Padrão, Profissional, Minimalista, Personalizado)
- **Template Integration**: Prompts da IA agora referem-se ao template customizado do usuário

### 🎨 Melhorias de UI/UX
- HUB page com design gradiente profissional
- Cards informativos com acesso rápido às configurações
- Editor de template com altura otimizada (250px textarea)
- Melhor organização das abas de configuração

### 📚 Documentação
- README.md completamente reescrito para v0.3
- Seções para Novidades, Configuração por aba, HUB Page e Templates
- Instruções de personalização e melhorias futuras
- Badges de versão e criador adicionadas

### 🔧 Mudanças Internas
- Adicionadas funções helpers: `wpai_get_default_template()` e `wpai_get_templates()`
- Prompt da IA agora integra o template customizado via `$custom_template`
- Plugin header atualizado: Versão 0.3, Autor "Richard & Automate AI"

### 📊 Informações Técnicas
- Arquivo principal: 19.9 KB (+4.7 KB vs v0.1)
- ZIP final: 12.5 KB
- Testes: 7/7 Passing ✓
- PHP Syntax: Valid ✓

---

## v0.2 (não registrada)

### ✨ Novas Funcionalidades (inferidas)
- Auto-save com Gutenberg + fallback AJAX
- Brief text generation
- Multiple insertion modes (content/excerpt/both)

---

## v0.1 (Release Inicial)

### ✨ Funcionalidades Base
- Settings page com chave de API
- Product metabox com botões gerar/inserir
- AJAX handler para chamar OpenAI
- HTML sanitization com wp_kses()
- Modern CSS com gradientes e animações
- Admin JS com validações e feedback
- Suporte a Classic + Gutenberg editor

### 🔒 Segurança
- Nonce protection em AJAX
- Capability checks (current_user_can)
- HTML sanitization (wp_kses, wp_strip_all_tags)
- Password input para API key

### 📝 Documentação
- README.md básico com instalação e uso
- Observações de segurança

---

## Roadmap Futuro

- [ ] Histórico de resumos gerados
- [ ] Cache de resultados
- [ ] Suporte a múltiplas IAs (Claude, Llama)
- [ ] Extração automática de atributos (preço, SKU, categorias)
- [ ] Controle de tom/linguagem na geração
- [ ] Preview do template antes de gerar
- [ ] Bulk generation para múltiplos produtos
- [ ] Analytics de geração

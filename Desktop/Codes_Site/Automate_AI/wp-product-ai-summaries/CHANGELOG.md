# CHANGELOG

## v0.3 (25/11/2025) - Richard Edition

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

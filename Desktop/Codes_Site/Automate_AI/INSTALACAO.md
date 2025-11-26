# Instruções de Instalação Manual

Se o upload do ZIP não funcionar no WordPress, siga estas etapas:

## Opção 1: Instalação via FTP/SFTP

1. **Conecte via FTP** ao seu servidor usando um cliente como FileZilla
2. **Navegue até** `wp-content/plugins/`
3. **Crie uma pasta** com o nome `wp-product-ai-summaries`
4. **Copie os seguintes arquivos** para essa pasta:
   - `wp-product-ai-summaries.php` (arquivo principal)
   - `admin.js`
   - `admin-settings.css`
   - `admin-metabox.css`
   - `admin-preview.css`
   - `README.md` (opcional, para referência)

5. **Ative o plugin** em **Dashboard → Plugins → Plugins instalados**
6. Procure por "WP Product AI Summaries" e clique em **Ativar**

## Opção 2: Instalação via SSH/Terminal

```bash
cd /home/seu-usuario/public_html/wp-content/plugins/
mkdir wp-product-ai-summaries
cd wp-product-ai-summaries
# Copie os arquivos aqui
```

## Opção 3: Descompactar ZIP localmente e fazer upload

1. **Descompacte** o arquivo `wp-product-ai-summaries-v0.6.zip` em seu computador
2. **Via WordPress Admin:**
   - Vá em **Plugins → Adicionar novo → Enviar plugin**
   - Escolha o arquivo ZIP
   - Clique em **Instalar agora**

OU

- **Via FTP:** Upload da pasta descompactada para `wp-content/plugins/`

## Após Instalação

1. **Ative o plugin** em Plugins instalados
2. Vá em **Configurações → AI Summaries** (ou use o link "Configurações" no painel de plugins)
3. Cole sua chave de API OpenAI
4. Configure o modelo e modos de inserção
5. Clique em **Salvar Configurações**
6. Edite um produto WooCommerce e use o metabox **"⚡ AI Summary"**

## ⚠️ Importante: Evitar Duplicatas

Se o plugin aparecer **múltiplas vezes** na lista, siga estes passos:

### Causa Comum
- ZIP com estrutura incorreta (arquivos soltos sem pasta pai `wp-product-ai-summaries/`)
- Múltiplas pastas antigas do plugin em `wp-content/plugins/`

### Solução
1. **Acesse `wp-content/plugins/` via FTP ou SSH**
2. **Delete todas as pastas** com nomes similares:
   - `wp-product-ai-summaries` (pasta antiga)
   - `wp-product-ai-summaries-old`
   - Qualquer outra pasta com "ai-summaries"

3. **Use apenas a v0.6** ou mais recente
4. **Certifique-se de que o ZIP contém** a estrutura correta:
   ```
   wp-product-ai-summaries/
   ├── wp-product-ai-summaries.php
   ├── admin.js
   ├── admin-settings.css
   ├── admin-metabox.css
   ├── admin-preview.css
   ├── README.md
   └── CHANGELOG.md
   ```

5. **Limpe o cache do WordPress**:
   - Deative plugins de cache temporariamente
   - Limpe cookies do navegador (Ctrl+Shift+Delete)
   - Recarregue a página do Dashboard

## Troubleshooting

| Problema | Solução |
|----------|---------|
| **"Nenhum plugin válido foi encontrado"** | Certifique-se de que `wp-product-ai-summaries.php` está no raiz da pasta do plugin e que o ZIP contém a pasta pai `wp-product-ai-summaries/` |
| **Plugin não aparece na lista** | Verifique se a pasta `wp-product-ai-summaries` está em `wp-content/plugins/` |
| **Plugin aparece múltiplas vezes** | Delete pastas antigas em `wp-content/plugins/`, limpe cache e recarregue (veja seção acima) |
| **Erro de permissão ao criar metabox** | Verifique se o usuário tem privilégios de editor de plugin em WordPress |
| **CSS/JS não carregam** | Verifique se os arquivos CSS/JS estão na mesma pasta do arquivo principal PHP; Limpe cache do navegador |
| **WooCommerce não detectado** | Certifique-se de que WooCommerce está instalado e ativado |
| **Erro ao gerar resumo** | Verifique se a chave de API OpenAI está correta nas configurações |

## Versões

- **v0.6**: HUB com abas, template preview, tutorial integrado (recomendado)
- v0.5: Auto-update notifier, WooCommerce dependency check
- v0.4: Template preview modals, interactive HTML/CSS guide
- v0.3: HUB page, template customization, auto-save
- v0.2: Modern UI/UX improvements
- v0.1: Base plugin

## Suporte

Se o problema persistir:
1. Consulte o **Tutorial** no HUB do plugin (Configurações → AI Summaries)
2. Verifique se o **WooCommerce está ativo**
3. Limpe o **cache completo** (plugins + navegador)
4. Verifique a **estrutura dos arquivos** em `wp-content/plugins/wp-product-ai-summaries/`

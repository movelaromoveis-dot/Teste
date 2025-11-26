(function($){
    $(document).ready(function(){
        var $metabox = $('#wpai_meta');
        var $btnGenerate = $('#wpai-generate');
        var $btnInsert = $('#wpai-insert');
        var $result = $('#wpai-result');

        // Create status message container
        var $statusMsg = $('<div class="wpai-status-message"></div>');
        $result.after($statusMsg);

        function showStatus(message, type) {
            $statusMsg
                .removeClass('success error loading')
                .addClass('show ' + type)
                .text(message)
                .fadeIn(200);
            
            setTimeout(function() {
                if (type !== 'loading') {
                    $statusMsg.fadeOut(300);
                }
            }, type === 'loading' ? 0 : 3000);
        }

        $('#wpai-generate').on('click', function(e){
            e.preventDefault();
            var post_id = wpaiData.post_id;

            $btnGenerate
                .prop('disabled', true);
            
            var originalText = $btnGenerate.text();
            $btnGenerate.html('<span class="wpai-spinner"></span><span class="wpai-spinner"></span><span class="wpai-spinner"></span> Gerando...');
            
            showStatus('Conectando com IA...', 'loading');

            $.post(wpaiData.ajax_url, {
                action: 'wpai_generate_summary',
                nonce: wpaiData.nonce,
                post_id: post_id
            }).done(function(res){
                if (res.success && res.data.html) {
                    $result.val(res.data.html);
                    // enable insert button
                    $btnInsert.prop('disabled', false);
                    showStatus('✓ Resumo gerado com sucesso! Clique em "Inserir" para usar.', 'success');
                    // if API returned brief_text and insert_target is excerpt-only, store it for insertion
                    if (res.data.brief_text && wpaiData.insert_target === 'excerpt') {
                        $result.data('brief_text', res.data.brief_text);
                    } else {
                        $result.removeData('brief_text');
                    }
                } else {
                    showStatus('✗ Erro: ' + (res.data || 'Resposta inesperada da API'), 'error');
                }
            }).fail(function(xhr, status, error){
                showStatus('✗ Erro na requisição: ' + error, 'error');
            }).always(function(){
                $btnGenerate
                    .prop('disabled', false)
                    .text(originalText);
            });
        });

        $('#wpai-insert').on('click', function(e){
            e.preventDefault();
            var html = $result.val();

            if (!html.trim()) {
                showStatus('⚠ Nenhum resumo gerado ainda. Clique em "Gerar" primeiro.', 'error');
                return;
            }

            var target = wpaiData.insert_target || 'excerpt';
            var brief = $result.data('brief_text') || null;

            // Insert according to target
            try {
                if (window.wp && wp.data && wp.data.dispatch) {
                    if (target === 'content') {
                        wp.data.dispatch('core/editor').editPost({content: html});
                    } else if (target === 'excerpt') {
                        var briefText = brief || $('<div>').html(html).text().trim().slice(0, 300);
                        wp.data.dispatch('core/editor').editPost({excerpt: briefText});
                    } else if (target === 'both') {
                        var briefText = brief || $('<div>').html(html).text().trim().slice(0, 300);
                        wp.data.dispatch('core/editor').editPost({content: html, excerpt: briefText});
                    }
                } else {
                    // Classic editor fallback
                    if (target === 'content') {
                        var $content = $('#content');
                        if ($content.length) $content.val(html);
                    } else if (target === 'excerpt') {
                        var $excerpt = $('#excerpt');
                        if ($excerpt.length) $excerpt.val(brief || $('<div>').html(html).text().trim().slice(0,300));
                    } else if (target === 'both') {
                        var $content = $('#content'); if ($content.length) $content.val(html);
                        var $excerpt = $('#excerpt'); if ($excerpt.length) $excerpt.val(brief || $('<div>').html(html).text().trim().slice(0,300));
                    }
                }

                // After insertion: try to auto-save
                showStatus('✓ Resumo inserido localmente. Salvando...', 'loading');

                // If Gutenberg editor available, use its save mechanism
                if (window.wp && wp.data && wp.data.select && wp.data.select('core/editor')) {
                    try {
                        wp.data.dispatch('core/editor').savePost().then(function() {
                            showStatus('✓ Resumo salvo no servidor com sucesso.', 'success');
                        }).catch(function(err) {
                            showStatus('⚠ Não foi possível salvar automaticamente no editor. Tentando fallback...', 'error');
                            // fallback to AJAX save
                            ajaxSave();
                        });
                    } catch (err) {
                        // fallback
                        ajaxSave();
                    }
                } else {
                    // no Gutenberg: use AJAX save fallback
                    ajaxSave();
                }

                function ajaxSave() {
                    var post_id = wpaiData.post_id;
                    var payload = { action: 'wpai_save_post', nonce: wpaiData.nonce, post_id: post_id };
                    if (target === 'content' || target === 'both') payload.content = html;
                    if (target === 'excerpt' || target === 'both') payload.excerpt = brief || $('<div>').html(html).text().trim().slice(0,300);

                    $.post(wpaiData.ajax_url, payload).done(function(resp){
                        if (resp.success) {
                            showStatus('✓ Resumo salvo no servidor com sucesso.', 'success');
                        } else {
                            showStatus('✗ Falha ao salvar no servidor: ' + (resp.data || 'erro'), 'error');
                        }
                    }).fail(function(){
                        showStatus('✗ Erro na requisição de salvamento.', 'error');
                    });
                }
            } catch (err) {
                // fallback: set excerpt if nothing else
                var $excerpt = $('#excerpt');
                if ($excerpt.length) {
                    $excerpt.val(brief || $('<div>').html(html).text().trim().slice(0,300));
                    showStatus('✓ Resumo inserido no campo Resumo (fallback). Salve o produto.', 'success');
                } else {
                    showStatus('✗ Não foi possível inserir automaticamente. Copie e cole manualmente.', 'error');
                }
            }
        });

        // Disable insert button initially if no content
        if (!$result.val().trim()) {
            $btnInsert.prop('disabled', true);
        }

        // Preview result button in metabox
        var $previewResultBtn = $('#wpai-preview-result');
        if ($previewResultBtn.length) {
            $previewResultBtn.on('click', function(e){
                e.preventDefault();
                var html = $result.val().trim();
                if (!html) {
                    alert('Gere um resumo primeiro para visualizar.');
                    return;
                }
                showResultPreview(html);
            });
        }

        function showResultPreview(html) {
            var $modal = $('<div class="wpai-modal wpai-result-preview-modal"></div>');
            var $overlay = $('<div class="wpai-modal-overlay"></div>');
            var $content = $('<div class="wpai-modal-content"><button class="wpai-modal-close">×</button><h2>Preview do Resultado</h2><div class="wpai-preview-frame"></div></div>');
            
            $content.find('.wpai-preview-frame').html(html);
            $modal.append($content);
            
            $('body').append($overlay).append($modal);
            
            $content.find('.wpai-modal-close').on('click', function(){
                $modal.remove();
                $overlay.remove();
            });
            $overlay.on('click', function(){
                $modal.remove();
                $overlay.remove();
            });
        }
    });
})(jQuery);

// Template Preview & Help Modal
(function($){
    $(document).ready(function(){
        // Preview button in template editor
        var $templateEditor = $('#wpai_template_editor');
        if ($templateEditor.length) {
            var $previewBtn = $('<button class="button wpai-preview-btn" style="margin-left: 10px; margin-top: 10px;" type="button">👁 Preview</button>');
            $templateEditor.after($previewBtn);
            
            $previewBtn.on('click', function(e){
                e.preventDefault();
                showTemplatePreview($templateEditor.val());
            });

            // Help button
            var $helpBtn = $('<button class="button wpai-help-btn" style="margin-left: 5px; margin-top: 10px;" type="button">❓ Ajuda HTML/CSS</button>');
            $previewBtn.after($helpBtn);
            
            $helpBtn.on('click', function(e){
                e.preventDefault();
                showHtmlCssGuide();
            });
        }

        function showTemplatePreview(template) {
            if (!template || template.trim().length === 0) {
                alert('Template vazio. Adicione HTML para preview.');
                return;
            }

            // Replace placeholder with sample content
            var sampleContent = '<strong>Produto Exemplo:</strong> Mesa elegante de jantar com design moderno, estrutura robusta em MDF.';
            var previewHtml = template.replace('{CONTEUDO}', sampleContent);
            
            // Create modal
            var $modal = $('<div class="wpai-modal wpai-preview-modal"></div>');
            var $overlay = $('<div class="wpai-modal-overlay"></div>');
            var $content = $('<div class="wpai-modal-content"><button class="wpai-modal-close">×</button><h2>Preview do Template</h2><div class="wpai-preview-frame"></div></div>');
            
            $content.find('.wpai-preview-frame').html(previewHtml);
            $modal.append($content);
            
            $('body').append($overlay).append($modal);
            
            // Close handlers
            $content.find('.wpai-modal-close').on('click', function(){
                $modal.remove();
                $overlay.remove();
            });
            $overlay.on('click', function(){
                $modal.remove();
                $overlay.remove();
            });
        }

        function showHtmlCssGuide() {
            var guideHtml = '<div class="wpai-guide-container"><h3>Guia de Tags HTML &amp; CSS para Templates</h3><div class="wpai-guide-section"><h4>📌 Placeholder Obrigatório</h4><p><strong>{CONTEUDO}</strong> - Lugar onde o resumo gerado pela IA será inserido</p></div><div class="wpai-guide-section"><h4>📝 Tags de Texto</h4><ul><li><code>&lt;h1&gt;Título&lt;/h1&gt;</code> - Título grande (página)</li><li><code>&lt;h3&gt;Subtítulo&lt;/h3&gt;</code> - Subtítulo médio</li><li><code>&lt;p&gt;Parágrafo&lt;/p&gt;</code> - Texto normal</li><li><code>&lt;strong&gt;Texto em negrito&lt;/strong&gt;</code> - Destaque</li></ul></div><div class="wpai-guide-section"><h4>📋 Tags de Lista</h4><ul><li><code>&lt;ul&gt; ... &lt;/ul&gt;</code> - Lista com pontos</li><li><code>&lt;li&gt;Item&lt;/li&gt;</code> - Cada item da lista</li></ul></div><div class="wpai-guide-section"><h4>📦 Tags de Estrutura</h4><ul><li><code>&lt;div&gt; ... &lt;/div&gt;</code> - Container/caixa (agrupa elementos)</li><li><code>&lt;table&gt;, &lt;tr&gt;, &lt;td&gt;</code> - Tabela com linhas e colunas</li></ul></div><div class="wpai-guide-section"><h4>🎨 Estilos CSS (dentro de &lt;style&gt;)</h4><p><strong>Exemplos de propriedades úteis:</strong></p><ul><li><code>color: #333;</code> - Cor do texto (#333 = cinza escuro)</li><li><code>background: #f9f9f9;</code> - Cor de fundo (#f9f9f9 = branco com toque cinza)</li><li><code>padding: 20px;</code> - Espaço interno (20 pixels)</li><li><code>margin: 15px 0;</code> - Espaço externo (15px acima/abaixo, 0 nas laterais)</li><li><code>border-left: 4px solid #ffc107;</code> - Borda esquerda (4px, cor ouro)</li><li><code>border-radius: 8px;</code> - Cantos arredondados</li><li><code>font-size: 16px;</code> - Tamanho do texto</li><li><code>line-height: 1.6;</code> - Espaço entre linhas (melhor legibilidade)</li></ul></div><div class="wpai-guide-section"><h4>✅ Template Exemplo</h4><pre><code>&lt;div style="padding: 20px; background: #f9f9f9; border-radius: 8px;"&gt;<br/>    &lt;h1 style="color: #333; margin-bottom: 15px;"&gt;Produto&lt;/h1&gt;<br/>    &lt;p style="color: #666; line-height: 1.6;"&gt;{CONTEUDO}&lt;/p&gt;<br/>    &lt;div style="background: #fff3cd; padding: 15px; margin-top: 20px; border-left: 4px solid #ffc107;"&gt;<br/>        &lt;strong&gt;Atenção:&lt;/strong&gt; Informação importante aqui.<br/>    &lt;/div&gt;<br/>&lt;/div&gt;</code></pre></div><div class="wpai-guide-section"><h4>⚠️ Tags Proibidas (segurança)</h4><p>Não use: &lt;script&gt;, &lt;iframe&gt;, &lt;form&gt;, onclick, onload, etc.</p></div></div>';

            var $modal = $('<div class="wpai-modal wpai-help-modal"></div>');
            var $overlay = $('<div class="wpai-modal-overlay"></div>');
            var $content = $('<div class="wpai-modal-content"><button class="wpai-modal-close">×</button><div class="wpai-guide-scrollable">' + guideHtml + '</div></div>');
            
            $modal.append($content);
            $('body').append($overlay).append($modal);
            
            // Close handlers
            $content.find('.wpai-modal-close').on('click', function(){
                $modal.remove();
                $overlay.remove();
            });
            $overlay.on('click', function(){
                $modal.remove();
                $overlay.remove();
            });
        }
    });
})(jQuery);

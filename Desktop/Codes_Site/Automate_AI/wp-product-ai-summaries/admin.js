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
    });
})(jQuery);

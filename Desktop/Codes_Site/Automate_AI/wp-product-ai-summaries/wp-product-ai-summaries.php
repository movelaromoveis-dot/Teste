<?php
/*
Plugin Name: WP Product AI Summaries
Plugin URI: https://github.com/movelaromoveis-dot/Teste
Description: Gera resumos HTML para produtos WooCommerce usando uma API de IA.
Version: 0.6.1-beta
Author: Richard
Author URI: 
Text Domain: wp-product-ai-summaries
License: GPL v2 or later
*/

defined('ABSPATH') or die();

define('WPAI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPAI_PLUGIN_FILE', __FILE__);
define('WPAI_PLUGIN_VERSION', '0.6.1-beta');
define('WPAI_PLUGIN_SLUG', 'wp-product-ai-summaries');
define('WPAI_GITHUB_REPO', 'movelaromoveis-dot/Teste');
define('WPAI_GITHUB_API', 'https://api.github.com/repos/' . WPAI_GITHUB_REPO . '/releases/latest');
define('WPAI_NONCE_ACTION', 'wpai_nonce_action');
define('WPAI_NONCE_NAME', 'wpai_nonce');

/* --- WooCommerce Dependency Check --- */
add_action('admin_notices', 'wpai_check_woocommerce_dependency');
function wpai_check_woocommerce_dependency() {
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        echo '<div class="notice notice-error is-dismissible"><p>';
        echo '<strong>WP Product AI Summaries:</strong> Este plugin requer WooCommerce instalado e ativado. ';
        echo '<a href="' . admin_url('plugin-install.php?tab=search&s=woocommerce') . '">Instalar WooCommerce</a>';
        echo '</p></div>';
    }
}

/* --- Version Update Checker & Notifier --- */
add_action('admin_notices', 'wpai_check_for_updates');
function wpai_check_for_updates() {
    if (!current_user_can('manage_options')) return;
    
    // Check for updates once per day
    $last_check = get_option('wpai_last_update_check', 0);
    if (time() - $last_check < 86400) return;
    
    $latest = wpai_get_latest_version();
    update_option('wpai_last_update_check', time());
    
    if ($latest && version_compare(WPAI_PLUGIN_VERSION, $latest, '<')) {
        echo '<div class="notice notice-info is-dismissible"><p>';
        echo '<strong>WP Product AI Summaries:</strong> ';
        echo 'Nova versão disponível: <strong>' . esc_html($latest) . '</strong> ';
        echo '(<a href="https://github.com/' . WPAI_GITHUB_REPO . '/releases/tag/v' . esc_attr($latest) . '" target="_blank">Ver detalhes</a>)';
        echo '</p></div>';
        update_option('wpai_latest_version', $latest);
    }
}

function wpai_get_latest_version() {
    $transient_key = 'wpai_latest_version_cache';
    $cached = get_transient($transient_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $response = wp_remote_get(WPAI_GITHUB_API, array(
        'timeout' => 5,
        'headers' => array('User-Agent' => 'wp-product-ai-summaries'),
    ));
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!isset($data['tag_name'])) {
        return false;
    }
    
    // Extract version number (v0.4 -> 0.4)
    $version = str_replace('v', '', $data['tag_name']);
    
    // Cache for 24 hours
    set_transient($transient_key, $version, 86400);
    
    return $version;
}

/* --- Check WooCommerce on plugin activation --- */
register_activation_hook(__FILE__, 'wpai_activation_check');
function wpai_activation_check() {
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        wp_die(
            'Este plugin requer WooCommerce. Por favor, instale e ative o WooCommerce primeiro.',
            'Dependência não atendida',
            array('back_link' => true)
        );
    }
}

/* --- Plugin action links (Configurações) --- */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wpai_add_plugin_links');
function wpai_add_plugin_links($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=wp-ai-summaries') . '">Configurações</a>';
    array_unshift($links, $settings_link);
    return $links;
}

/* --- HUB page (custom admin page with dashboard) --- */
add_action('admin_menu', 'wpai_add_hub_menu', 5);
function wpai_add_hub_menu() {
    add_menu_page(
        'WP Product AI',
        'WP Product AI',
        'manage_options',
        'wp-product-ai-hub',
        'wpai_hub_page',
        'dashicons-sparkles',
        26
    );
}

function wpai_hub_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Você não tem permissão para acessar esta página.', 'wp-product-ai-summaries'));
    }
    
    $current_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'dashboard';
    $allowed_tabs = array('dashboard', 'templates', 'tutorial');
    if (!in_array($current_tab, $allowed_tabs, true)) {
        $current_tab = 'dashboard';
    }
    ?>
    <div class="wrap wpai-hub">
        <div class="wpai-hub-header">
            <h1>WP Product AI Summaries</h1>
            <p class="subtitle">Gere resumos HTML para produtos WooCommerce com IA</p>
            <p class="wpai-version">Versão <?php echo esc_html(WPAI_PLUGIN_VERSION); ?> | Criador: <strong>Richard</strong></p>
        </div>

        <!-- Tabs Navigation -->
        <nav class="wpai-tabs">
            <a href="?page=wp-product-ai-hub&tab=dashboard" class="tab-link <?php echo $current_tab === 'dashboard' ? 'active' : ''; ?>">📊 Dashboard</a>
            <a href="?page=wp-product-ai-hub&tab=templates" class="tab-link <?php echo $current_tab === 'templates' ? 'active' : ''; ?>">🎨 Visualizar Templates</a>
            <a href="?page=wp-product-ai-hub&tab=tutorial" class="tab-link <?php echo $current_tab === 'tutorial' ? 'active' : ''; ?>">📖 Tutorial</a>
        </nav>

        <!-- Tab Content -->
        <div class="wpai-tab-content">
            <?php
            if ($current_tab === 'dashboard') {
                wpai_hub_dashboard();
            } elseif ($current_tab === 'templates') {
                wpai_hub_templates();
            } elseif ($current_tab === 'tutorial') {
                wpai_hub_tutorial();
            }
            ?>
        </div>
    </div>
    <style>
        .wpai-hub { max-width: 1200px; margin: 0 auto; }
        .wpai-hub-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; text-align: center; }
        .wpai-hub-header h1 { margin: 0; font-size: 32px; }
        .wpai-hub-header .subtitle { margin: 10px 0 0 0; opacity: 0.9; }
        .wpai-version { margin: 15px 0 0 0; font-size: 14px; opacity: 0.95; }
        
        .wpai-tabs { display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; }
        .tab-link { padding: 12px 24px; color: #667eea; text-decoration: none; font-weight: 600; transition: all 0.3s; cursor: pointer; border-bottom: 3px solid transparent; }
        .tab-link:hover { color: #764ba2; }
        .tab-link.active { color: #764ba2; border-bottom-color: #764ba2; }
        
        .wpai-tab-content { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        .wpai-hub-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .wpai-hub-card { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s; }
        .wpai-hub-card:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.12); transform: translateY(-2px); }
        .wpai-hub-card h3 { margin-top: 0; color: #2d3748; }
        .wpai-hub-card p { color: #666; line-height: 1.6; }
        .wpai-hub-card .button { margin-top: 15px; }
        
        .wpai-template-preview { background: #f9f9f9; border: 1px solid #ddd; border-radius: 6px; padding: 20px; margin-bottom: 20px; }
        .wpai-template-preview h4 { margin-top: 0; color: #333; }
        .wpai-template-preview iframe { width: 100%; height: 300px; border: 1px solid #ddd; border-radius: 4px; }
        
        .wpai-tutorial-section { background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin-bottom: 20px; }
        .wpai-tutorial-section h3 { color: #667eea; margin-top: 0; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .wpai-tutorial-section h4 { color: #333; margin-top: 20px; }
        .wpai-tutorial-section ul { line-height: 1.8; color: #555; }
        .wpai-tutorial-section li { margin-bottom: 10px; }
        .wpai-tutorial-section code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .wpai-step-box { background: #f0f4ff; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 15px; border-radius: 4px; }
        .wpai-step-box strong { color: #667eea; }
        .wpai-tip-box { background: #fffbf0; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 15px; border-radius: 4px; }
        .wpai-tip-box strong { color: #ff9800; }
    </style>
    <?php
}

function wpai_hub_dashboard() {
    ?>
    <div class="wpai-hub-content">
        <div class="wpai-hub-card">
            <h3>⚙️ Configurações</h3>
            <p>Configure sua chave de API, escolha onde inserir resumos e customize o template HTML.</p>
            <a href="<?php echo admin_url('admin.php?page=wp-ai-summaries'); ?>" class="button button-primary">Abrir Configurações</a>
        </div>

        <div class="wpai-hub-card">
            <h3>🎨 Templates</h3>
            <p>Visualize e customize o formato HTML dos seus resumos de forma prática.</p>
            <a href="?page=wp-product-ai-hub&tab=templates" class="button">Ver Templates</a>
        </div>

        <div class="wpai-hub-card">
            <h3>📖 Aprenda a Usar</h3>
            <p>Tutorial passo-a-passo para começar a gerar resumos com a IA.</p>
            <a href="?page=wp-product-ai-hub&tab=tutorial" class="button">Acessar Tutorial</a>
        </div>

        <div class="wpai-hub-card">
            <h3>ℹ️ Sobre</h3>
            <p><strong>Versão:</strong> <?php echo esc_html(WPAI_PLUGIN_VERSION); ?><br/><strong>Criador:</strong> Richard<br/><strong>Tecnologia:</strong> OpenAI API</p>
        </div>
    </div>
    <?php
}

function wpai_hub_templates() {
    $opts = get_option('wpai_options', array());
    $current_template = isset($opts['template_custom']) ? $opts['template_custom'] : wpai_get_default_template();
    ?>
    <div class="wpai-tutorial-section">
        <h3>🎨 Visualizador de Templates</h3>
        <p>Veja como seu template ficará com conteúdo real:</p>
        
        <div class="wpai-template-preview">
            <h4>Preview Atual:</h4>
            <?php
            $sample_content = '<strong>Produto Exemplo:</strong> Mesa elegante de jantar com design moderno, estrutura robusta em MDF. Perfeita para ambientes sofisticados.';
            $preview_html = str_replace('{CONTEUDO}', $sample_content, $current_template);
            echo wp_kses_post($preview_html);
            ?>
        </div>

        <p><strong>Dica:</strong> Para alterar este template, vá em <a href="<?php echo admin_url('admin.php?page=wp-ai-summaries'); ?>">Configurações → Templates HTML</a> e edite o campo "Editor de Template".</p>
    </div>
    <?php
}

function wpai_hub_tutorial() {
    ?>
    <div class="wpai-tutorial-section">
        <h3>📖 Tutorial: Como Usar o Plugin</h3>
        
        <div class="wpai-step-box">
            <strong>Passo 1: Configurar a Chave de API</strong>
            <ol style="margin: 10px 0 0 0;">
                <li>Vá em <a href="<?php echo admin_url('admin.php?page=wp-ai-summaries'); ?>">WP Product AI → Configurações</a></li>
                <li>Abra a aba <strong>"Configurações da IA"</strong></li>
                <li>Copie sua chave de API do OpenAI em <code>https://platform.openai.com/api-keys</code></li>
                <li>Cole a chave no campo <strong>"API Key"</strong> e clique <strong>"Salvar Alterações"</strong></li>
            </ol>
        </div>

        <h4>✨ Configurações Recomendadas</h4>
        <ul>
            <li><strong>Modelo:</strong> Deixe em branco para usar <code>gpt-4o-mini</code> (mais rápido e barato)</li>
            <li><strong>Inserir em:</strong> Escolha <strong>"Ambos (both)"</strong> para preencher tanto descrição quanto resumo</li>
            <li><strong>Gerar resumo breve:</strong> Marque para gerar texto simples + HTML</li>
        </ul>

        <div class="wpai-step-box" style="margin-top: 20px;">
            <strong>Passo 2: Editar um Produto e Gerar Resumo</strong>
            <ol style="margin: 10px 0 0 0;">
                <li>Vá em <strong>Produtos → Todos os Produtos</strong></li>
                <li>Clique em um produto para editar (ou crie um novo)</li>
                <li>Desça até encontrar o metabox <strong>"⚡ AI Summary"</strong></li>
                <li>Clique no botão <strong>"✨ Gerar Resumo IA"</strong> e aguarde (alguns segundos)</li>
                <li>O HTML será gerado e exibido. Revise e clique em <strong>"✓ Inserir no Resumo"</strong></li>
                <li>O produto será salvo automaticamente</li>
            </ol>
        </div>

        <div class="wpai-tip-box">
            <strong>💡 Dica:</strong> Você pode editar o HTML gerado antes de inserir! Copie o texto do campo "Resultado HTML", modifique conforme quiser e clique "Inserir".
        </div>

        <h4>🎨 Customizando o Template</h4>
        <div class="wpai-step-box">
            <strong>Para mudar o formato dos resumos:</strong>
            <ol style="margin: 10px 0 0 0;">
                <li>Vá em <a href="<?php echo admin_url('admin.php?page=wp-ai-summaries'); ?>">Configurações → Templates HTML</a></li>
                <li>Abra a aba <strong>"Templates HTML"</strong></li>
                <li>No campo <strong>"Editor de Template"</strong>, customize o HTML</li>
                <li>Use <code>{CONTEUDO}</code> como placeholder para onde o resumo será inserido</li>
                <li>Clique <strong>"Salvar Alterações"</strong></li>
                <li>Próximos resumos gerados usarão seu novo template!</li>
            </ol>
        </div>

        <p>Precisa de ajuda com HTML/CSS? Clique no botão <strong>"❓ Ajuda HTML/CSS"</strong> no editor de templates para ver exemplos.</p>

        <h4>❓ Perguntas Frequentes</h4>
        <ul>
            <li><strong>Posso voltar um resumo gerado?</strong> Sim! Clique em "Editar" no produto, copie a descrição anterior de um backup e clique "Atualizar".</li>
            <li><strong>A IA precisa de internet?</strong> Sim, faz requisições para OpenAI. Você precisa de uma chave de API válida.</li>
            <li><strong>O resumo é gerado em segundos?</strong> Normalmente sim. Se demorar muito, pode ser latência da rede ou limite da API.</li>
            <li><strong>Consigo gerar para vários produtos de uma vez?</strong> Não nesta versão. Cada produto deve ser gerado individualmente.</li>
        </ul>

        <div class="wpai-tip-box">
            <strong>⚠️ Importante:</strong> Este plugin <strong>requer WooCommerce</strong> instalado e ativado para funcionar. Sem WooCommerce, o plugin não funcionará.
        </div>
    </div>
    <?php
}



/* --- Settings page (API key / model) --- */
add_action('admin_menu', 'wpai_add_admin_menu');
function wpai_add_admin_menu() {
    add_options_page('AI Summaries', 'AI Summaries', 'manage_options', 'wp-ai-summaries', 'wpai_settings_page');
}

add_action('admin_init','wpai_settings_init');
function wpai_settings_init() {
    // Sanitize callback for options
    $sanitize_cb = function($options) {
        if (!is_array($options)) {
            return array();
        }
        
        $sanitized = array();
        
        if (isset($options['api_key'])) {
            $sanitized['api_key'] = sanitize_text_field(wp_unslash($options['api_key']));
        }
        if (isset($options['model'])) {
            $sanitized['model'] = sanitize_text_field(wp_unslash($options['model']));
        }
        if (isset($options['insert_target'])) {
            $valid_targets = array('excerpt', 'content', 'both');
            $sanitized['insert_target'] = in_array($options['insert_target'], $valid_targets, true) 
                ? sanitize_text_field(wp_unslash($options['insert_target'])) 
                : 'excerpt';
        }
        if (isset($options['generate_brief'])) {
            $sanitized['generate_brief'] = (bool) $options['generate_brief'];
        }
        if (isset($options['template_selected'])) {
            $sanitized['template_selected'] = sanitize_text_field(wp_unslash($options['template_selected']));
        }
        if (isset($options['template_custom'])) {
            $sanitized['template_custom'] = wp_kses_post(wp_unslash($options['template_custom']));
        }
        
        return $sanitized;
    };
    
    register_setting('wpai_settings', 'wpai_options', array(
        'sanitize_callback' => $sanitize_cb,
        'type' => 'array'
    ));

    add_settings_section('wpai_section','Configurações da IA','wpai_section_cb','wp-ai-summaries');

    add_settings_field('wpai_api_key','API Key','wpai_api_key_cb','wp-ai-summaries','wpai_section');
    add_settings_field('wpai_model','Modelo (opcional)','wpai_model_cb','wp-ai-summaries','wpai_section');
    add_settings_field('wpai_insert_target','Inserir em','wpai_insert_target_cb','wp-ai-summaries','wpai_section');
    add_settings_field('wpai_generate_brief','Gerar resumo breve','wpai_generate_brief_cb','wp-ai-summaries','wpai_section');

    add_settings_section('wpai_templates','Templates HTML','wpai_templates_section_cb','wp-ai-summaries');
    add_settings_field('wpai_template_select','Selecionar Template','wpai_template_select_cb','wp-ai-summaries','wpai_templates');
    add_settings_field('wpai_template_editor','Editor de Template','wpai_template_editor_cb','wp-ai-summaries','wpai_templates');
}
function wpai_section_cb() {
    echo '<p>Adicione sua chave de API e preferências de onde inserir o resumo gerado.</p>';
}

function wpai_templates_section_cb() {
    echo '<p>Customize o formato HTML dos resumos gerados pela IA. Use {CONTEUDO} como placeholder para o resumo.</p>';
}

function wpai_template_select_cb() {
    $opts = get_option('wpai_options', array());
    $selected_template = isset($opts['template_selected']) ? esc_attr($opts['template_selected']) : 'default';
    $templates = wpai_get_templates();
    echo '<select name="wpai_options[template_selected]" id="wpai_template_select">';
    foreach ($templates as $key => $label) {
        echo '<option value="' . esc_attr($key) . '"' . selected($selected_template, $key, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<p class="description">Escolha o template padrão para novos resumos.</p>';
}

function wpai_template_editor_cb() {
    $opts = get_option('wpai_options', array());
    $custom_template = isset($opts['template_custom']) ? $opts['template_custom'] : wpai_get_default_template();
    echo '<textarea name="wpai_options[template_custom]" id="wpai_template_editor" style="width:100%; height:250px; font-family:monospace;">' . wp_kses_post($custom_template) . '</textarea>';
    echo '<p class="description">Cole seu HTML customizado aqui. Use <strong>{CONTEUDO}</strong> onde o resumo deve ir.</p>';
}

function wpai_api_key_cb() {
    $opts = get_option('wpai_options', array());
    $key = isset($opts['api_key']) ? esc_attr($opts['api_key']) : '';
    echo '<input type="password" name="wpai_options[api_key]" value="' . $key . '" style="width:420px">';
    echo '<p class="description">Cole a chave da sua conta de IA (ex: OpenAI).</p>';
}

function wpai_model_cb() {
    $opts = get_option('wpai_options', array());
    $model = isset($opts['model']) ? esc_attr($opts['model']) : '';
    echo '<input type="text" name="wpai_options[model]" value="' . $model . '" placeholder="ex: gpt-4o-mini" style="width:420px">';
    echo '<p class="description">Deixe em branco para usar o padrão.</p>';
}

function wpai_insert_target_cb() {
    $opts = get_option('wpai_options', array());
    $val = isset($opts['insert_target']) ? $opts['insert_target'] : 'excerpt';
    $choices = array(
        'excerpt' => 'Resumo (excerpt) - breve descrição',
        'content' => 'Descrição completa (content)',
        'both' => 'Ambos (coloca a descrição completa e preenche o resumo breve)'
    );
    echo '<select name="wpai_options[insert_target]" style="width:320px">';
    foreach ($choices as $k => $label) {
        $sel = ($k === $val) ? ' selected' : '';
        echo "<option value=\"$k\"$sel>$label</option>";
    }
    echo '</select>';
    echo '<p class="description">Escolha onde o HTML gerado será inserido ao clicar em Inserir.</p>';
}

function wpai_generate_brief_cb() {
    $opts = get_option('wpai_options', array());
    $val = isset($opts['generate_brief']) && $opts['generate_brief'] ? 'checked' : '';
    echo '<label><input type="checkbox" name="wpai_options[generate_brief]" value="1" ' . $val . '> Pedir ao modelo também um resumo breve (apto para excerpt)</label>';
    echo '<p class="description">Quando marcado, a IA retornará também um resumo breve que pode ser inserido no campo Resumo (excerpt).</p>';
}

function wpai_settings_page() {
    ?>
    <div class="wrap">
        <h1>WP Product AI Summaries</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wpai_settings');
            do_settings_sections('wp-ai-summaries');
            submit_button();
            ?>
        </form>
        <p>Depois de salvar a chave, edite um produto WooCommerce e use o metabox "AI Summary" para gerar um resumo.</p>
    </div>
    <?php
}

/* --- Metabox on product edit --- */
add_action('add_meta_boxes','wpai_add_meta_box');
function wpai_add_meta_box() {
    add_meta_box('wpai_meta', 'AI Summary', 'wpai_meta_box_cb', 'product', 'side', 'high');
}

function wpai_meta_box_cb($post) {
    wp_nonce_field('wpai_nonce_action','wpai_nonce');
    $generated = get_post_meta($post->ID, '_wpai_generated_summary', true);
    ?>
    <div class="wpai-metabox-section">
        <label class="wpai-metabox-label">Gerar Resumo Automático</label>
        <p style="margin: 0 0 12px 0; font-size: 13px; color: #718096;">
            Clique abaixo para gerar um resumo HTML otimizado para este produto usando IA.
        </p>
        <div class="wpai-button-group">
            <button type="button" class="button" id="wpai-generate">Gerar Resumo IA</button>
        </div>
    </div>

    <div class="wpai-metabox-section">
        <label class="wpai-metabox-label">Resultado HTML</label>
        <span class="wpai-metabox-subtitle">Copie, edite ou clique em "Inserir" para usar</span>
        <textarea id="wpai-result" style="min-height: 160px;"><?php echo esc_textarea($generated); ?></textarea>
        <button type="button" class="button" id="wpai-preview-result" style="margin-top: 10px;">👁 Preview Resultado</button>
    </div>

    <div class="wpai-metabox-section">
            <div class="wpai-button-group">
            <button type="button" class="button" id="wpai-insert">Inserir</button>
        </div>
    </div>
    <?php
}

add_action('admin_enqueue_scripts','wpai_admin_scripts');
function wpai_admin_scripts($hook) {
    global $post;
    
    // Settings page styles
    if ($hook === 'settings_page_wp-ai-summaries') {
        wp_enqueue_style('wpai-settings-css', plugins_url('admin-settings.css', __FILE__), array(), '0.3');
        wp_enqueue_style('wpai-preview-css', plugins_url('admin-preview.css', __FILE__), array(), '0.3');
    }
    
    // Product edit page
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        if (isset($post) && $post->post_type === 'product') {
            wp_enqueue_style('wpai-metabox-css', plugins_url('admin-metabox.css', __FILE__), array(), '0.3');
            wp_enqueue_style('wpai-preview-css', plugins_url('admin-preview.css', __FILE__), array(), '0.3');
            wp_enqueue_script('wpai-admin-js', plugins_url('admin.js', __FILE__), array('jquery'), '0.3', true);
            $opts = get_option('wpai_options', array());
            $nonce = wp_create_nonce('wpai_nonce');
            wp_localize_script('wpai-admin-js', 'wpaiData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => $nonce,
                'post_id' => $post->ID,
                'insert_target' => isset($opts['insert_target']) ? $opts['insert_target'] : 'excerpt',
                'generate_brief' => isset($opts['generate_brief']) && $opts['generate_brief'] ? true : false,
            ));
        }
    }
}

/* --- AJAX handler: call external AI API --- */
add_action('wp_ajax_wpai_generate_summary','wpai_generate_summary');
function wpai_generate_summary() {
    if (!current_user_can('edit_posts')) wp_send_json_error('Permissão negada');
    check_ajax_referer('wpai_nonce','nonce');

    $post_id = intval($_POST['post_id']);
    if (!$post_id) wp_send_json_error('Post ID inválido');

    $post = get_post($post_id);
    if (!$post) wp_send_json_error('Produto não encontrado');

    // Build prompt from product data
    $title = $post->post_title;
    $excerpt = $post->post_excerpt;
    $content = $post->post_content;

    $product_data = "Título: $title\nResumo atual: $excerpt\nDescrição completa: $content\n";

    // You can extend: attributes, SKU, price, categories

    $opts = get_option('wpai_options', array());
    $api_key = isset($opts['api_key']) ? $opts['api_key'] : '';
    $model = isset($opts['model']) && !empty($opts['model']) ? $opts['model'] : 'gpt-4o-mini';
    $insert_target = isset($opts['insert_target']) ? $opts['insert_target'] : 'excerpt';
    $generate_brief = isset($opts['generate_brief']) && $opts['generate_brief'];

    if (empty($api_key)) wp_send_json_error('Chave de API não configurada. Configure em Settings → AI Summaries.');

    // Get user's custom template or default
    $custom_template = isset($opts['template_custom']) && !empty($opts['template_custom']) ? $opts['template_custom'] : wpai_get_default_template();
    $template_instructions = "Use este template como referência para o HTML:\n$custom_template\n";

    // Default prompt: generate full HTML
    $prompt = <<<PROMPT
Você é um assistente especializado em gerar resumos HTML para produtos WooCommerce. 
Gere um HTML estruturado e elegante. Referência de template (substitua conforme necessário):

$template_instructions


Use estas tags HTML APENAS: <div>, <h1>, <h3>, <p>, <ul>, <li>, <table>, <tr>, <td>, <strong>, <style>.
Inclua um bloco <style> com CSS para formatação profissional.
NÃO use <script>, style inline ou HTML perigoso.

Informações do produto:
$product_data

Responda APENAS com o HTML completo, sem explicações adicionais.
PROMPT;

    $body = array(
        'model' => $model,
        'messages' => array(
            array('role' => 'system', 'content' => 'Você escreve resumos curtos em HTML para produtos WooCommerce.'),
            array('role' => 'user', 'content' => $prompt),
        ),
        'temperature' => 0.6,
        'max_tokens' => 600,
    );

    // If user requested brief or wants both, ask the model to return JSON with both full_html and brief_text
    if ($generate_brief || $insert_target === 'both') {
        $prompt = <<<PROMPT
Você é um assistente especializado em gerar resumos HTML para produtos WooCommerce.

Além do campo full_html, gere também um campo brief_text (texto simples, 1-2 frases, até 160 caracteres) pronto para ser usado no campo Resumo (excerpt).

Responda APENAS com um objeto JSON válido contendo as chaves: full_html (string com o HTML completo) e brief_text (string com o resumo breve). Não adicione explicações extras.

Informações do produto:
$product_data
PROMPT;

        $body = array(
            'model' => $model,
            'messages' => array(
                array('role' => 'system', 'content' => 'Retorne SOMENTE um objeto JSON com full_html e brief_text.'),
                array('role' => 'user', 'content' => $prompt),
            ),
            'temperature' => 0.6,
            'max_tokens' => 800,
        );
    }
    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'body' => wp_json_encode($body),
        'timeout' => 30,
    ));

    if (is_wp_error($response)) {
        wp_send_json_error('Erro na requisição externa: ' . $response->get_error_message());
    }

    $code = wp_remote_retrieve_response_code($response);
    $resp_body = wp_remote_retrieve_body($response);
    $data = json_decode($resp_body, true);

    if ($code !== 200 || !isset($data['choices'][0]['message']['content'])) {
        wp_send_json_error('Resposta inválida da API: ' . substr($resp_body, 0, 200));
    }

    $content = $data['choices'][0]['message']['content'];

    // Sanitize: allow basic tags suitable for product descriptions
    $allowed = array(
        'div' => array('class' => array()),
        'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(),
        'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
        'strong' => array(), 'em' => array(), 'br' => array(),
        'a' => array('href' => array(), 'rel' => array(), 'target' => array()),
        'span' => array('class' => array()),
        'img' => array('src' => array(), 'alt' => array(), 'width' => array(), 'height' => array()),
        'table' => array('class' => array()), 'thead' => array(), 'tbody' => array(), 'tfoot' => array(),
        'tr' => array(), 'td' => array(), 'th' => array(),
        'style' => array(),
    );

    // If the model returned JSON with full_html and brief_text, try to parse it
    $full_html = '';
    $brief_text = '';
    if ($generate_brief || $insert_target === 'both') {
        $first = strpos($content, '{');
        $last = strrpos($content, '}');
        if ($first !== false && $last !== false && $last > $first) {
            $maybe = substr($content, $first, $last - $first + 1);
            $obj = json_decode($maybe, true);
            if ($obj && (isset($obj['full_html']) || isset($obj['brief_text']))) {
                $full_html = isset($obj['full_html']) ? $obj['full_html'] : '';
                $brief_text = isset($obj['brief_text']) ? $obj['brief_text'] : '';
            } else {
                $full_html = $content;
            }
        } else {
            $full_html = $content;
        }
    } else {
        $full_html = $content;
    }

    $safe = wp_kses($full_html, $allowed);

    // Save generated snippet as post meta so user can keep it
    update_post_meta($post_id, '_wpai_generated_summary', $safe);

    $response_payload = array('html' => $safe);
    if (!empty($brief_text)) {
        $response_payload['brief_text'] = wp_strip_all_tags($brief_text);
    }

    wp_send_json_success($response_payload);
}

/* --- AJAX handler: save post content/excerpt on server (fallback) --- */
add_action('wp_ajax_wpai_save_post','wpai_save_post');
function wpai_save_post() {
    if (!current_user_can('edit_posts')) wp_send_json_error('Permissão negada');
    check_ajax_referer('wpai_nonce','nonce');

    $post_id = intval($_POST['post_id']);
    if (!$post_id) wp_send_json_error('Post ID inválido');

    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
    $excerpt = isset($_POST['excerpt']) ? wp_strip_all_tags($_POST['excerpt']) : '';

    $update = array('ID' => $post_id);
    if (!empty($content)) $update['post_content'] = $content;
    if (!empty($excerpt) || $excerpt === '') $update['post_excerpt'] = $excerpt;

    $result = wp_update_post($update, true);
    if (is_wp_error($result)) {
        wp_send_json_error('Erro ao salvar post: ' . $result->get_error_message());
    }

    wp_send_json_success(array('saved' => true, 'post_id' => $post_id));
}

/* --- Insert into product excerpt via AJAX is handled in admin.js (client side) --- */

/* --- Template Helpers --- */
function wpai_get_default_template() {
    return <<<TEMPLATE
<div class="product-summary" style="font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; border-radius: 8px;">
    <h1 style="color: #333; margin-bottom: 15px;">{TITULO}</h1>
    <p style="color: #666; line-height: 1.6; margin-bottom: 20px;">{CONTEUDO}</p>
    <div class="product-disclaimer" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px; border-radius: 4px;">
        <strong style="color: #d39e00;">Atenção:</strong>
        <p style="margin: 10px 0 0 0; color: #666; font-size: 0.9em;">Objetos decorativos não acompanham o produto. Imagem meramente ilustrativa. As cores e detalhes podem variar conforme lote e iluminação.</p>
    </div>
</div>
TEMPLATE;
}

function wpai_get_templates() {
    return array(
        'default' => 'Padrão (Simples)',
        'professional' => 'Profissional (Completo)',
        'minimal' => 'Minimalista',
        'custom' => 'Personalizado',
    );
}

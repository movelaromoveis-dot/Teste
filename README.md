<h1>WooCommerce AI Addons – README</h1>

<h2>🇧🇷 Português</h2>

<h3>📌 Sobre o Projeto</h3>
<p>
  Sou um Dev Junior tentando aprender PHP criando alguns plugins/addon que ajudem o dia a dia com tarefas repetivas em sites eCommerce
</p>
<p>
Este projeto é um plugin para <strong>WordPress + WooCommerce</strong> criado para facilitar tarefas repetitivas e melhorar o fluxo de trabalho de lojas virtuais. Ele inclui um conjunto de <strong>addons inteligentes</strong>, com foco no uso de <strong>Inteligência Artificial</strong> para gerar resumos automáticos de produtos, detectar itens duplicados e permitir personalização avançada.
</p>
<p>
No momento o plugin usa a <strong>API da OpenAI</strong>, porém foi construído de forma extensível para suportar <strong>múltiplas IAs</strong> no futuro — bastando ao usuário inserir a chave de API desejada.
</p>

<hr>

<h3>🚀 Funcionalidades Principais</h3>

<h4>🧠 1. Geração de Resumos com IA</h4>
<ul>
  <li>Geração automática de resumos para produtos do WooCommerce</li>
  <li>Uso atual da API OpenAI</li>
  <li>Suporte planejado para múltiplos provedores de IA</li>
  <li>Sistema de <strong>templates personalizados em HTML</strong></li>
  <li>Geração integrada diretamente na página de edição do produto</li>
</ul>

<h4>🧩 2. Sistema de Addons</h4>
<ul>
  <li>Estrutura modular para expansão fácil</li>
  <li><strong>Addon de Varredura de Produtos Duplicados</strong> (em desenvolvimento)</li>
  <li>Permite ativar/desativar addons sem alterar o núcleo</li>
</ul>

<h4>🔑 3. Suporte a Múltiplas APIs de IA (planejado)</h4>
<ul>
  <li>Integração futura com diversos provedores</li>
  <li>Basta inserir a chave de API desejada</li>
</ul>

<hr>

<h3>🛠️ Instalação</h3>
<ol>
  <li>Baixe ou clone este repositório.</li>
  <li>Envie os arquivos para <code>wp-content/plugins/</code>.</li>
  <li>Ative o plugin no painel WordPress.</li>
  <li>Configure a API Key (OpenAI, por enquanto) dentro das configurações do plugin.</li>
</ol>

<hr>

<h3>📝 Criando Templates em HTML para IA</h3>
<p>
O plugin permite que você personalize totalmente o conteúdo gerado, usando templates em HTML. Você pode estruturar o resumo exatamente como deseja que apareça na descrição do produto.
</p>

<p>Variáveis disponíveis:</p>
<ul>
  <li><code>{title}</code> – título do produto</li>
  <li><code>{price}</code> – preço do produto</li>
  <li><code>{attributes}</code> – atributos formatados</li>
  <li><code>{description}</code> – descrição bruta</li>
</ul>

<p>Você pode criar diferentes templates para diferentes categorias ou tipos de produto e aplicá-los com um clique.</p>

<hr>

<h3>🔍 Addon: Varredura de Produtos Duplicados</h3>
<p>Este addon identifica possíveis duplicidades com base em:</p>
<ul>
  <li>Título</li>
  <li>SKU</li>
  <li>Similaridade textual</li>
  <li>Regras configuráveis</li>
</ul>
<p>Funções:</p>
<ul>
  <li>Listar possíveis duplicados</li>
  <li>Mesclar ou corrigir manualmente</li>
  <li>Relatórios automáticos (em breve)</li>
</ul>

<hr>

<h3>📅 Roadmap</h3>
<ul>
  <li>✔ Templates personalizáveis em HTML</li>
  <li>✔ Integração com OpenAI</li>
  <li>✔ Addon de duplicados (em desenvolvimento)</li>
  <li>⏳ Suporte a múltiplas IAs</li>
  <li>⏳ Painel de métricas</li>
  <li>⏳ Otimização automática de SEO</li>
</ul>

<hr>

<h3>🤝 Contribuindo</h3>
<p>Pull requests são bem-vindos! Feedbacks e sugestões também.</p>

<hr>

<h3>📄 Licença</h3>
<p>Este projeto está sob a licença MIT.</p>

<hr>

<h2>🇺🇸 English</h2>

<h3>📌 About the Project</h3>
<p>
  I'm a junior dev trying to learn PHP by creating plugins/addons to help with repetitive tasks on eCommerce websites.
</p>
<p>
This project is a <strong>WordPress + WooCommerce</strong> plugin designed to simplify repetitive tasks and enhance online store productivity. It includes a set of <strong>smart addons</strong>, focusing on the use of <strong>Artificial Intelligence</strong> to generate product summaries, detect duplicates, and support advanced customization.
</p>
<p>
It currently uses the <strong>OpenAI API</strong>, but the architecture is prepared to support <strong>multiple AI providers</strong> in the future, requiring only the user to add an API key.
</p>

<hr>

<h3>🚀 Main Features</h3>

<h4>🧠 1. AI Summary Generation</h4>
<ul>
  <li>Automatic summary generation for WooCommerce products</li>
  <li>Currently uses OpenAI API</li>
  <li>Planned support for multiple AIs</li>
  <li>Fully customizable <strong>HTML templates</strong></li>
  <li>Product editor integration</li>
</ul>

<h4>🧩 2. Addon System</h4>
<ul>
  <li>Modular and expandable architecture</li>
  <li><strong>Duplicate Product Scanner Addon</strong> (in progress)</li>
  <li>Easy activation/deactivation of addons</li>
</ul>

<h4>🔑 3. Multi-AI Support (planned)</h4>
<ul>
  <li>Add any AI provider by inserting the API key</li>
  <li>Prepared for OpenAI, Anthropic, Gemini, Groq, etc.</li>
</ul>

<hr>

<h3>🛠️ Installation</h3>
<ol>
  <li>Download or clone this repository.</li>
  <li>Upload it to <code>wp-content/plugins/</code>.</li>
  <li>Activate the plugin in WordPress admin.</li>
  <li>Insert your API key (OpenAI for now).</li>
</ol>

<hr>

<h3>📝 HTML Templates for AI</h3>
<p>The plugin allows you to fully customize the generated summary using HTML templates.</p>
<p>Available variables:</p>
<ul>
  <li><code>{title}</code></li>
  <li><code>{price}</code></li>
  <li><code>{attributes}</code></li>
  <li><code>{description}</code></li>
</ul>

<hr>

<h3>🔍 Addon: Duplicate Product Scanner</h3>
<ul>
  <li>Title comparison</li>
  <li>SKU matching</li>
  <li>Text similarity</li>
  <li>Configurable rules</li>
</ul>

<hr>

<h3>📅 Roadmap</h3>
<ul>
  <li>✔ Custom HTML templates</li>
  <li>✔ OpenAI support</li>
  <li>✔ Duplicate scanner addon (WIP)</li>
  <li>⏳ Multi-AI support</li>
  <li>⏳ Metrics panel</li>
  <li>⏳ Automated SEO optimization</li>
</ul>

<hr>

<h3>🤝 Contributing</h3>
<p>Pull requests are welcome! Ideas and suggestions are appreciated.</p>

<hr>

<h3>📄 License</h3>
<p>This project is released under the MIT license.</p>

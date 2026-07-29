<?php
/**
 * 2026 ARGSEGURIDAD
 * AdminController for argscssjs module v1.0.5
 * Standardized update process matching AdminArgsSellersController architecture
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminArgsCssJsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->meta_title = $this->l('CSS y JS Personalizado');
        $this->toolbar_title = $this->l('CSS y JS Personalizado');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_btn['update_module'] = array(
            'href' => $this->context->link->getAdminLink('AdminArgsCssJs') . '&processUpdateModule=1',
            'desc' => $this->l('Actualizar Módulo'),
            'icon' => 'process-icon-refresh',
        );
    }

    public function initContent()
    {
        if (isset($this->context->cookie->argscssjs_conf)) {
            $type = isset($this->context->cookie->argscssjs_conf_type) ? $this->context->cookie->argscssjs_conf_type : 'success';
            if ($type === 'warning') {
                $this->warnings[] = $this->context->cookie->argscssjs_conf;
            } else {
                $this->confirmations[] = $this->context->cookie->argscssjs_conf;
            }
            unset($this->context->cookie->argscssjs_conf);
            unset($this->context->cookie->argscssjs_conf_type);
        }

        $this->content .= $this->renderForm();
        parent::initContent();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('processUpdateModule')) {
            $this->processUpdateModule();
            return;
        }

        if (Tools::isSubmit('submitArgsCssJs')) {
            $css = Tools::getValue('ARGCSSJS_CUSTOM_CSS');
            $js = Tools::getValue('ARGCSSJS_CUSTOM_JS');

            Configuration::updateValue('ARGCSSJS_CUSTOM_CSS', $css, true);
            Configuration::updateValue('ARGCSSJS_CUSTOM_JS', $js, true);

            $this->confirmations[] = $this->l('Código CSS y JS guardado correctamente.');
        }
    }

    public function getFieldsForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuración de CSS y JS Personalizado'),
                    'icon' => 'icon-code',
                ),
                'input' => array(
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Código CSS Personalizado'),
                        'name' => 'ARGCSSJS_CUSTOM_CSS',
                        'cols' => 80,
                        'rows' => 15,
                        'desc' => $this->l('Escribí tus reglas de CSS aquí. Se inyectarán automáticamente en la tienda.'),
                        'class' => 'codemirror-css',
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Código JavaScript Personalizado'),
                        'name' => 'ARGCSSJS_CUSTOM_JS',
                        'cols' => 80,
                        'rows' => 20,
                        'desc' => $this->l('Escribí tus scripts de JS aquí. Se inyectarán automáticamente en la tienda.'),
                        'class' => 'codemirror-js',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Guardar Cambios'),
                    'class' => 'btn btn-primary pull-right',
                ),
            ),
        );
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'configuration';
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = 'id_configuration';
        $helper->submit_action = 'submitArgsCssJs';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminArgsCssJs', false);
        $helper->token = Tools::getAdminTokenLite('AdminArgsCssJs');

        $helper->fields_value['ARGCSSJS_CUSTOM_CSS'] = Configuration::get('ARGCSSJS_CUSTOM_CSS');
        $helper->fields_value['ARGCSSJS_CUSTOM_JS'] = Configuration::get('ARGCSSJS_CUSTOM_JS');

        // Dark IDE styling for textareas
        $this->context->smarty->assign(array(
            'dark_editor_css' => '<style>
                textarea.codemirror-css {
                    font-family: Consolas, Monaco, "Andale Mono", monospace !important;
                    background: #1e293b !important;
                    color: #38bdf8 !important;
                    font-size: 14px !important;
                    line-height: 1.5 !important;
                    padding: 14px !important;
                    border-radius: 8px !important;
                    border: 1px solid #334155 !important;
                }
                textarea.codemirror-js {
                    font-family: Consolas, Monaco, "Andale Mono", monospace !important;
                    background: #0f172a !important;
                    color: #facc15 !important;
                    font-size: 14px !important;
                    line-height: 1.5 !important;
                    padding: 14px !important;
                    border-radius: 8px !important;
                    border: 1px solid #334155 !important;
                }
            </style>'
        ));

        return $helper->generateForm(array($this->getFieldsForm()));
    }

    public function processUpdateModule()
    {
        @ini_set('max_execution_time', 120);
        @ini_set('memory_limit', '256M');

        $version_before = $this->module->version;
        $github_repo = 'magnus2201/argcssjs';
        $download_urls = array(
            'https://codeload.github.com/' . $github_repo . '/zip/refs/heads/main',
            'https://github.com/' . $github_repo . '/archive/refs/heads/main.zip',
            'https://raw.githubusercontent.com/' . $github_repo . '/main/argscssjs.zip'
        );

        $zip_file = _PS_MODULE_DIR_ . $this->module->name . '_update.zip';
        $file_downloaded = false;
        $download_error = '';

        foreach ($download_urls as $download_url) {
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $download_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PrestaShop-Updater');
                curl_setopt($ch, CURLOPT_TIMEOUT, 25);
                $file_data = curl_exec($ch);
                $last_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($last_http_code == 200 && !empty($file_data) && strlen($file_data) > 500) {
                    file_put_contents($zip_file, $file_data);
                    $file_downloaded = true;
                    break;
                }
            }

            if (!$file_downloaded && ini_get('allow_url_fopen')) {
                $opts = array(
                    'http' => array(
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 PrestaShop-Updater\r\n",
                        'timeout' => 25
                    ),
                    'ssl' => array('verify_peer' => false, 'verify_peer_name' => false)
                );
                $stream_data = @file_get_contents($download_url, false, stream_context_create($opts));
                if (!empty($stream_data) && strlen($stream_data) > 500) {
                    file_put_contents($zip_file, $stream_data);
                    $file_downloaded = true;
                    break;
                }
            }
        }

        $version_after = $version_before;
        if (file_exists($zip_file) && class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zip_file) === true) {
                $temp_extract = _PS_MODULE_DIR_ . $this->module->name . '_extracted_temp/';
                if (!file_exists($temp_extract)) {
                    mkdir($temp_extract, 0755, true);
                }
                $zip->extractTo($temp_extract);
                $zip->close();

                $subfolders = glob($temp_extract . '*', GLOB_ONLYDIR);
                $source_dir = $temp_extract;
                if (!empty($subfolders)) {
                    foreach ($subfolders as $sf) {
                        if (file_exists($sf . '/argscssjs.php')) {
                            $source_dir = $sf . '/';
                            break;
                        } elseif (file_exists($sf . '/argscssjs/argscssjs.php')) {
                            $source_dir = $sf . '/argscssjs/';
                            break;
                        }
                    }
                }

                $new_module_file = $source_dir . 'argscssjs.php';
                if (file_exists($new_module_file)) {
                    $new_content = file_get_contents($new_module_file);
                    if (preg_match("/\\\$this->version\s*=\s*'([^']+)'/", $new_content, $matches)) {
                        $version_after = $matches[1];
                    }
                }

                $this->rcopy($source_dir, _PS_MODULE_DIR_ . $this->module->name . '/');

                if (class_exists('Tools') && method_exists('Tools', 'deleteDirectory')) {
                    Tools::deleteDirectory($temp_extract, true);
                }
                @unlink($zip_file);
            }
        }

        if (method_exists($this->module, 'runUpgradeModule')) {
            $this->module->runUpgradeModule();
        }

        try {
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
        } catch (Exception $e) {
        }

        if ($file_downloaded) {
            $msg = 'Módulo actualizado correctamente desde GitHub (' . $github_repo . '). Versión anterior: v' . $version_before . ' -> Nueva versión: v' . $version_after . '. Cache purgada.';
            $this->context->cookie->argscssjs_conf = $msg;
            $this->context->cookie->argscssjs_conf_type = 'success';
        } else {
            $msg = 'No se pudo descargar la actualización desde GitHub. Verifica la conexión.';
            $this->context->cookie->argscssjs_conf = $msg;
            $this->context->cookie->argscssjs_conf_type = 'warning';
        }

        Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
    }

    private function rcopy($src, $dst)
    {
        if (!file_exists($src)) return;
        if (is_dir($src)) {
            if (!file_exists($dst)) mkdir($dst, 0755, true);
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $this->rcopy("$src/$file", "$dst/$file");
                }
            }
        } else if (file_exists($src)) {
            copy($src, $dst);
        }
    }
}

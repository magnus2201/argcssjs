<?php
/**
 * 2026 ARGSEGURIDAD
 * AdminController for argscssjs module v1.0.2
 * Fix: HelperForm single render & prevent ObjectModel Configuration->name error
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

        $before_version = $this->module->version;
        $urls = array(
            'https://codeload.github.com/magnus2201/argcssjs/zip/refs/heads/main',
            'https://github.com/magnus2201/argcssjs/archive/refs/heads/main.zip',
            'https://raw.githubusercontent.com/magnus2201/argcssjs/main/argscssjs.zip'
        );

        $module_dir = _PS_MODULE_DIR_ . $this->module->name . '/';
        $tmp_zip = _PS_MODULE_DIR_ . 'argscssjs_update.zip';

        $downloaded = false;
        $download_method = '';

        foreach ($urls as $url) {
            // Method 1: cURL
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PrestaShop-Updater');
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                $content = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code == 200 && !empty($content) && strlen($content) > 500) {
                    file_put_contents($tmp_zip, $content);
                    $downloaded = true;
                    $download_method = 'cURL (' . parse_url($url, PHP_URL_HOST) . ')';
                    break;
                }
            }

            // Method 2: file_get_contents
            if (ini_get('allow_url_fopen')) {
                $opts = array(
                    'http' => array(
                        'method' => 'GET',
                        'header' => "User-Agent: PrestaShop-Updater\r\n",
                        'follow_location' => 1,
                        'timeout' => 20
                    ),
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    )
                );
                $context = stream_context_create($opts);
                $content = @file_get_contents($url, false, $context);
                if (!empty($content) && strlen($content) > 500) {
                    file_put_contents($tmp_zip, $content);
                    $downloaded = true;
                    $download_method = 'file_get_contents (' . parse_url($url, PHP_URL_HOST) . ')';
                    break;
                }
            }

            // Method 3: copy
            if (ini_get('allow_url_fopen')) {
                if (@copy($url, $tmp_zip) && file_exists($tmp_zip) && filesize($tmp_zip) > 500) {
                    $downloaded = true;
                    $download_method = 'copy (' . parse_url($url, PHP_URL_HOST) . ')';
                    break;
                }
            }
        }

        if (!$downloaded) {
            $this->errors[] = $this->l('No se pudo descargar la actualización desde GitHub. Verifica la conexión del servidor.');
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($tmp_zip) === true) {
            $extract_tmp = _PS_MODULE_DIR_ . 'argscssjs_tmp_extract/';
            if (!file_exists($extract_tmp)) {
                mkdir($extract_tmp, 0755, true);
            }
            $zip->extractTo($extract_tmp);
            $zip->close();
            @unlink($tmp_zip);

            $source_dir = '';
            if (file_exists($extract_tmp . 'argscssjs-main/')) {
                $source_dir = $extract_tmp . 'argscssjs-main/';
            } elseif (file_exists($extract_tmp . 'argscssjs/')) {
                $source_dir = $extract_tmp . 'argscssjs/';
            } else {
                $source_dir = $extract_tmp;
            }

            $this->recursiveCopy($source_dir, $module_dir);
            $this->recursiveRemoveDir($extract_tmp);

            Tools::clearAllCache();

            if (class_exists('Module')) {
                $updated_module = Module::getInstanceByName('argscssjs');
                if ($updated_module && method_exists($updated_module, 'runUpgradeModule')) {
                    $updated_module->runUpgradeModule();
                }
            }

            $after_module = Module::getInstanceByName('argscssjs');
            $after_version = $after_module ? $after_module->version : 'desconocida';

            if ($before_version === $after_version) {
                $this->warnings[] = sprintf(
                    $this->l('El módulo ya se encuentra en la última versión (%s). Se refrescaron todos los archivos correctamente.'),
                    $after_version
                );
            } else {
                $this->confirmations[] = sprintf(
                    $this->l('¡Módulo actualizado con éxito! Versión anterior: %s | Nueva versión instalada: %s (Vía %s).'),
                    $before_version,
                    $after_version,
                    $download_method
                );
            }
        } else {
            @unlink($tmp_zip);
            $this->errors[] = $this->l('El archivo comprimido descargado está dañado o no es un ZIP válido.');
        }
    }

    private function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function recursiveRemoveDir($dir)
    {
        if (!file_exists($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recursiveRemoveDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}

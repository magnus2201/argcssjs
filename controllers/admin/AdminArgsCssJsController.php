<?php
/**
 * 2026 ARGSEGURIDAD
 * AdminController for argscssjs module
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminArgsCssJsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'configuration';
        $this->className = 'Configuration';
        $this->identifier = 'id_configuration';
        $this->lang = false;

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
        $this->display = 'edit';
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

        parent::postProcess();
    }

    public function renderForm()
    {
        $this->fields_form = array(
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
                    'desc' => $this->l('Escribí tus reglas de CSS aquí. Se inyectarán automáticamente en el <head> de la tienda.'),
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
        );

        $this->fields_value['ARGCSSJS_CUSTOM_CSS'] = Configuration::get('ARGCSSJS_CUSTOM_CSS');
        $this->fields_value['ARGCSSJS_CUSTOM_JS'] = Configuration::get('ARGCSSJS_CUSTOM_JS');

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

        return parent::renderForm();
    }

    public function processUpdateModule()
    {
        @ini_set('max_execution_time', 120);
        @ini_set('memory_limit', '256M');

        $before_version = $this->module->version;
        $repo_url = 'https://github.com/magnus2201/argcssjs/archive/refs/heads/main.zip';
        $fallback_zip_url = 'https://raw.githubusercontent.com/magnus2201/argcssjs/main/argscssjs.zip';

        $module_dir = _PS_MODULE_DIR_ . $this->module->name . '/';
        $tmp_zip = _PS_MODULE_DIR_ . 'argscssjs_update.zip';

        $downloaded = false;
        $download_method = '';

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $repo_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PrestaShop-Updater');
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $content = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 200 && !empty($content) && strlen($content) > 1000) {
                file_put_contents($tmp_zip, $content);
                $downloaded = true;
                $download_method = 'cURL GitHub Archive';
            }
        }

        if (!$downloaded && function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fallback_zip_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PrestaShop-Updater');
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $content = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 200 && !empty($content) && strlen($content) > 1000) {
                file_put_contents($tmp_zip, $content);
                $downloaded = true;
                $download_method = 'cURL Direct ZIP Fallback';
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

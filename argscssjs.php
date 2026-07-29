<?php
/**
 * 2026 ARGSEGURIDAD
 * Module: argscssjs (CSS y JS) v1.0.4
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ArgsCssJs extends Module
{
    public function __construct()
    {
        $this->name = 'argscssjs';
        $this->tab = 'front_office_features';
        $this->version = '1.0.5';
        $this->author = 'ARGSEGURIDAD';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CSS y JS');
        $this->description = $this->l('Administrador de CSS y JS personalizado para la tienda con editor oscuro y auto-actualizador.');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function getDefaultCss()
    {
        return '/* Contact Page 4-Column Navigation System */
@media (min-width: 992px) {
  /* Flexbox order for tab titles: Vendedores 1st, Servicio Tecnico 2nd, Agendar Reunion 3rd, Sucursales 4th */
  .vendedoresText { order: 1 !important; }
  .servicioTecnicoText { order: 2 !important; }
  .agendarReunionText { order: 3 !important; }
  .sucursalesText { order: 4 !important; }

  /* Tab Buttons styling & visibility */
  .vendedoresText,
  .servicioTecnicoText,
  .agendarReunionText,
  .sucursalesText {
    cursor: pointer !important;
    padding: 12px 18px !important;
    border-radius: 8px 8px 0 0 !important;
    transition: all 0.2s ease-in-out !important;
    user-select: none !important;
    border-bottom: 3px solid transparent !important;
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 18px !important;
    text-align: center !important;
  }

  .vendedoresText:hover,
  .servicioTecnicoText:hover,
  .agendarReunionText:hover,
  .sucursalesText:hover {
    color: #0284c7 !important;
    background: #f0f9ff !important;
  }

  /* Active selected tab — high contrast & clear text */
  .vendedoresText.active-tab,
  .servicioTecnicoText.active-tab,
  .agendarReunionText.active-tab,
  .sucursalesText.active-tab {
    color: #0284c7 !important;
    background: #e0f2fe !important;
    border-bottom: 4px solid #0284c7 !important;
    font-weight: 800 !important;
  }

  .vendedoresText.active-tab *,
  .servicioTecnicoText.active-tab *,
  .agendarReunionText.active-tab *,
  .sucursalesText.active-tab * {
    color: #0284c7 !important;
    font-weight: 800 !important;
  }

  /* Expandable content sections */
  .infoVendedores.desplegable,
  .infoServicioTecnico.desplegable,
  .infoAgendarReunion.desplegable,
  .infoSucursales.desplegable {
    display: none !important;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    padding-top: 20px !important;
    padding-bottom: 40px !important;
    margin-bottom: 30px !important;
    clear: both !important;
    overflow: hidden !important;
  }

  .desplegable.active-section {
    display: block !important;
    opacity: 1 !important;
  }
}

/* Hide big ARGSEGURIDAD watermark logo image in contact sections */
.infoVendedores img[src*="logo"],
.infoServicioTecnico img[src*="logo"],
.infoAgendarReunion img[src*="logo"],
.infoSucursales img[src*="logo"],
.infoVendedores img[src*="ARGSEGURIDAD"],
.infoServicioTecnico img[src*="ARGSEGURIDAD"],
.infoAgendarReunion img[src*="ARGSEGURIDAD"],
.infoSucursales img[src*="ARGSEGURIDAD"],
.desplegable img[src*="ARGSEGURIDAD"] {
  display: none !important;
}

/* Fix footer overlap issue by forcing clearing & min-height on main CMS container */
#content.page-content,
.cms-id-7 #content,
.page-cms-7 #content {
  clear: both !important;
  min-height: 500px !important;
  overflow: hidden !important;
}

/* Seller Grid spacing fix inside .infoVendedores */
.infoVendedores .argsellers-container {
  margin-top: 20px !important;
  margin-bottom: 40px !important;
  clear: both !important;
}';
    }

    public function getDefaultJs()
    {
        return '(function initContactTabs() {
  function run() {
    if (window.innerWidth < 992) return;

    var tabs = [
      { btn: \'.vendedoresText\', target: \'.infoVendedores\' },
      { btn: \'.servicioTecnicoText\', target: \'.infoServicioTecnico\' },
      { btn: \'.agendarReunionText\', target: \'.infoAgendarReunion\' },
      { btn: \'.sucursalesText\', target: \'.infoSucursales\' }
    ];

    function activateTab(index) {
      tabs.forEach(function(item, i) {
        var btns = document.querySelectorAll(item.btn);
        var targets = document.querySelectorAll(item.target);

        if (i === index) {
          btns.forEach(function(b) { b.classList.add(\'active-tab\'); });
          targets.forEach(function(t) { t.classList.add(\'active-section\'); });
        } else {
          btns.forEach(function(b) { b.classList.remove(\'active-tab\'); });
          targets.forEach(function(t) { t.classList.remove(\'active-section\'); });
        }
      });
    }

    activateTab(0);

    tabs.forEach(function(item, idx) {
      document.querySelectorAll(item.btn).forEach(function(btn) {
        btn.addEventListener(\'click\', function() {
          activateTab(idx);
        });
      });
    });
  }

  if (document.readyState === \'loading\') {
    document.addEventListener(\'DOMContentLoaded\', run);
  } else {
    run();
  }
})();';
    }

    public function install()
    {
        Configuration::updateValue('ARGCSSJS_CUSTOM_CSS', $this->getDefaultCss());
        Configuration::updateValue('ARGCSSJS_CUSTOM_JS', $this->getDefaultJs());

        if (!$this->installTab()) {
            return false;
        }

        return parent::install() &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('ARGCSSJS_CUSTOM_CSS');
        Configuration::deleteByName('ARGCSSJS_CUSTOM_JS');

        $this->uninstallTab();

        return parent::uninstall();
    }

    public function installTab()
    {
        $id_parent = (int)Tab::getIdFromClassName('AdminParentThemes');
        if (!$id_parent) {
            $id_parent = (int)Tab::getIdFromClassName('AdminDesign');
        }

        $tab = new Tab();
        $tab->class_name = 'AdminArgsCssJs';
        $tab->id_parent = $id_parent;
        $tab->module = $this->name;
        $tab->active = 1;

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $tab->name[$lang['id_lang']] = 'CSS y JS';
        }

        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminArgsCssJs');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminArgsCssJs'));
    }

    public function hookDisplayHeader($params)
    {
        $css = Configuration::get('ARGCSSJS_CUSTOM_CSS');
        $js = Configuration::get('ARGCSSJS_CUSTOM_JS');

        $this->context->smarty->assign(array(
            'argcssjs_custom_css' => $css,
            'argcssjs_custom_js' => $js,
        ));

        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    public function runUpgradeModule()
    {
        if (class_exists('Module')) {
            $up_file = _PS_MODULE_DIR_ . $this->name . '/upgrade/upgrade-1.0.4.php';
            if (file_exists($up_file)) {
                include_once($up_file);
                if (function_exists('upgrade_module_1_0_4')) {
                    return upgrade_module_1_0_4($this);
                }
            }
        }
        return true;
    }
}

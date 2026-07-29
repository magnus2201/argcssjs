<?php
/**
 * 2026 ARGSEGURIDAD
 * Module: argscssjs (CSS y JS)
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
        $this->version = '1.0.1';
        $this->author = 'ARGSEGURIDAD';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CSS y JS');
        $this->description = $this->l('Administrador de CSS y JS personalizado para la tienda con editor oscuro y auto-actualizador.');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $default_css = '@media (min-width: 992px) {
  .infoVendedores.desplegable,
  .infoServicioTecnico.desplegable,
  .infoAgendarReunion.desplegable,
  .infoSucursales.desplegable {
    display: none !important;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }

  .desplegable.active-section {
    display: block !important;
    opacity: 1 !important;
  }

  .vendedoresText,
  .servicioTecnicoText,
  .agendarReunionText,
  .sucursalesText {
    cursor: pointer !important;
    padding: 10px 14px !important;
    border-radius: 8px 8px 0 0 !important;
    transition: all 0.2s ease !important;
    user-select: none !important;
    border-bottom: 3px solid transparent !important;
  }

  .vendedoresText:hover,
  .servicioTecnicoText:hover,
  .agendarReunionText:hover,
  .sucursalesText:hover {
    color: #009de0 !important;
    background: rgba(0, 157, 224, 0.06) !important;
  }

  .vendedoresText.active-tab,
  .servicioTecnicoText.active-tab,
  .agendarReunionText.active-tab,
  .sucursalesText.active-tab {
    color: #009de0 !important;
    border-bottom: 3px solid #009de0 !important;
    font-weight: 700 !important;
  }
}';

        $default_js = '(function initContactTabs() {
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

        Configuration::updateValue('ARGCSSJS_CUSTOM_CSS', $default_css);
        Configuration::updateValue('ARGCSSJS_CUSTOM_JS', $default_js);

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
        return true;
    }
}

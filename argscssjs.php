<?php
/**
 * 2026 ARGSEGURIDAD
 * Module: argscssjs (CSS y JS) v1.0.9
 * Fix: High-specificity Elementor column width override (100% width for Vendedores, Centered Servicio Tecnico & Agendar Reunion, JS DOM Tab Reorder)
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
        $this->version = '1.0.9';
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
        return '/* =========================================================
   1. NAVEGACIÓN Y PESTAÑAS (4 COLUMNAS)
   ========================================================= */
@media (min-width: 992px) {
  .vendedoresText,
  .servicioTecnicoText,
  .agendarReunionText,
  .sucursalesText {
    cursor: pointer !important;
    padding: 14px 20px !important;
    border-radius: 10px 10px 0 0 !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    user-select: none !important;
    border-bottom: 3px solid transparent !important;
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 18px !important;
    text-align: center !important;
    margin: 0 auto !important;
  }

  .vendedoresText:hover,
  .servicioTecnicoText:hover,
  .agendarReunionText:hover,
  .sucursalesText:hover {
    color: #0284c7 !important;
    background: #f0f9ff !important;
    transform: translateY(-2px) !important;
  }

  .vendedoresText:active,
  .servicioTecnicoText:active,
  .agendarReunionText:active,
  .sucursalesText:active {
    transform: scale(0.96) !important;
  }

  /* Pestaña activa seleccionada (Alto Contraste) */
  .vendedoresText.active-tab,
  .servicioTecnicoText.active-tab,
  .agendarReunionText.active-tab,
  .sucursalesText.active-tab {
    color: #0284c7 !important;
    background: #e0f2fe !important;
    border-bottom: 4px solid #0284c7 !important;
    font-weight: 800 !important;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08) !important;
  }

  .vendedoresText.active-tab *,
  .servicioTecnicoText.active-tab *,
  .agendarReunionText.active-tab *,
  .sucursalesText.active-tab * {
    color: #0284c7 !important;
    font-weight: 800 !important;
  }

  /* Secciones desplegables */
  .infoVendedores.desplegable,
  .infoServicioTecnico.desplegable,
  .infoAgendarReunion.desplegable,
  .infoSucursales.desplegable {
    display: none !important;
    opacity: 0;
    padding-top: 25px !important;
    padding-bottom: 40px !important;
    margin-bottom: 30px !important;
    clear: both !important;
    width: 100% !important;
  }

  /* Animación suave de aparición al hacer clic */
  .desplegable.active-section {
    display: block !important;
    animation: tabContentFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
  }
}

@keyframes tabContentFadeIn {
  0% {
    opacity: 0;
    transform: translateY(16px) scale(0.99);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* =========================================================
   2. ALTA ESPECIFICIDAD PARA FORZAR 100% ANCHO EN CONTENIDO
   ========================================================= */
.infoVendedores div[class*="elementor-column"],
.infoServicioTecnico div[class*="elementor-column"],
.infoAgendarReunion div[class*="elementor-column"],
div.infoVendedores div.elementor-column,
div.infoServicioTecnico div.elementor-column,
div.infoAgendarReunion div.elementor-column,
.infoVendedores .elementor-widget-wrap,
.infoServicioTecnico .elementor-widget-wrap,
.infoAgendarReunion .elementor-widget-wrap,
.infoVendedores .argsellers-container {
  width: 100% !important;
  max-width: 100% !important;
  min-width: 100% !important;
  flex: 0 0 100% !important;
  float: none !important;
  margin-left: 0 !important;
  margin-right: 0 !important;
  padding-left: 0 !important;
  padding-right: 0 !important;
}

/* Ocultar 2ª columna vacía de Elementor donde estaba la marca de agua */
.infoVendedores div[class*="elementor-column"]:nth-child(2),
.infoServicioTecnico div[class*="elementor-column"]:nth-child(2),
.infoAgendarReunion div[class*="elementor-column"]:nth-child(2) {
  display: none !important;
  width: 0 !important;
  flex: 0 0 0 !important;
}

.argsellers-grid {
  display: flex !important;
  flex-wrap: nowrap !important;
  justify-content: space-around !important;
  width: 100% !important;
  gap: 15px !important;
}

.argseller-card {
  flex: 1 1 15% !important;
  min-width: 140px !important;
}

/* =========================================================
   3. CENTRADO PERFECTO DE SERVICIO TÉCNICO Y AGENDAR REUNIÓN
   ========================================================= */
.infoServicioTecnico,
.infoAgendarReunion,
.infoServicioTecnico *,
.infoAgendarReunion * {
  text-align: center !important;
}

.infoServicioTecnico .elementor-widget,
.infoAgendarReunion .elementor-widget,
.infoServicioTecnico .elementor-container,
.infoAgendarReunion .elementor-container {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

.infoServicioTecnico a,
.infoAgendarReunion a,
.infoServicioTecnico .elementor-button,
.infoAgendarReunion .elementor-button {
  display: inline-block !important;
  margin: 20px auto !important;
}

/* =========================================================
   4. OCULTAR LOGO GIGANTE Y LIMPIEZA DE FOOTER
   ========================================================= */
.desplegable img[src*="ARGSEGURIDAD"],
.desplegable img[src*="logo"] {
  display: none !important;
}

.desplegable::after {
  content: "";
  display: table;
  clear: both;
}';
    }

    public function getDefaultJs()
    {
        return '(function initContactTabs() {
  function run() {
    if (window.innerWidth < 992) return;

    // 1. Reordenamiento físico en el DOM de las 4 columnas de los botones
    var vBtn = document.querySelector(\'.vendedoresText\');
    var stBtn = document.querySelector(\'.servicioTecnicoText\');
    var arBtn = document.querySelector(\'.agendarReunionText\');
    var sucBtn = document.querySelector(\'.sucursalesText\');

    if (vBtn && stBtn && arBtn && sucBtn) {
      var vCol = vBtn.closest(\'.elementor-column\') || vBtn;
      var stCol = stBtn.closest(\'.elementor-column\') || stBtn;
      var arCol = arBtn.closest(\'.elementor-column\') || arBtn;
      var sucCol = sucBtn.closest(\'.elementor-column\') || sucBtn;

      var parentRow = vCol.parentNode;
      if (parentRow) {
        parentRow.appendChild(vCol);
        parentRow.appendChild(stCol);
        parentRow.appendChild(arCol);
        parentRow.appendChild(sucCol);
      }
    }

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
            $up_file = _PS_MODULE_DIR_ . $this->name . '/upgrade/upgrade-1.0.9.php';
            if (file_exists($up_file)) {
                include_once($up_file);
                if (function_exists('upgrade_module_1_0_9')) {
                    return upgrade_module_1_0_9($this);
                }
            }
        }
        return true;
    }
}

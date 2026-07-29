<?php
/**
 * Upgrade script for argscssjs v1.0.9
 * - High-specificity Elementor column width override (100% width for Vendedores, Centered Servicio Tecnico & Agendar Reunion, JS DOM Tab Reorder)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_9($module)
{
    if (method_exists($module, 'getDefaultCss') && method_exists($module, 'getDefaultJs')) {
        Configuration::updateValue('ARGCSSJS_CUSTOM_CSS', $module->getDefaultCss(), true);
        Configuration::updateValue('ARGCSSJS_CUSTOM_JS', $module->getDefaultJs(), true);
    }
    return true;
}

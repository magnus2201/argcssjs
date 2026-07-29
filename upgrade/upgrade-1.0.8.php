<?php
/**
 * Upgrade script for argscssjs v1.0.8
 * - Auto-updates ARGCSSJS_CUSTOM_CSS & ARGCSSJS_CUSTOM_JS with JS DOM reordering & 100% width column expansion
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_8($module)
{
    if (method_exists($module, 'getDefaultCss') && method_exists($module, 'getDefaultJs')) {
        Configuration::updateValue('ARGCSSJS_CUSTOM_CSS', $module->getDefaultCss(), true);
        Configuration::updateValue('ARGCSSJS_CUSTOM_JS', $module->getDefaultJs(), true);
    }
    return true;
}

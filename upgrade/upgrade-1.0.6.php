<?php
/**
 * Upgrade script for argscssjs v1.0.6
 * - Sets overflow: visible to unclip page width and restore full grid width
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_6($module)
{
    if (method_exists($module, 'getDefaultCss') && method_exists($module, 'getDefaultJs')) {
        Configuration::updateValue('ARGCSSJS_CUSTOM_CSS', $module->getDefaultCss(), true);
        Configuration::updateValue('ARGCSSJS_CUSTOM_JS', $module->getDefaultJs(), true);
    }
    return true;
}

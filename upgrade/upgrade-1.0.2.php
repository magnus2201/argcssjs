<?php
/**
 * Upgrade script for argscssjs v1.0.2
 * - Single HelperForm render
 * - Fix ObjectModel Configuration->name error on submit
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_2($module)
{
    return true;
}

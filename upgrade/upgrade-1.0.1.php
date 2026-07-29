<?php
/**
 * Upgrade script for argscssjs v1.0.1
 * - Adds direct codeload download URL + 3 fallback download methods for GitHub updates
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($module)
{
    return true;
}

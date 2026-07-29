<?php
/**
 * Upgrade script for argscssjs v1.0.3
 * - Smart source_dir detection for GitHub archives matching repo name argcssjs
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_3($module)
{
    return true;
}

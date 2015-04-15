<?php
/**
 * @package SMF Code Syntax Highlighter Mod
 * @author digger http://mysmf.ru
 * @copyright 2015
 * @license MIT http://opensource.org/licenses/mit-license.php
 * @version 2.0
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
    die('Admin privileges required.');

// List settings here in the format: setting_key => default_value.  Escape any "s. (" => \")
$mod_settings = array(
    'code_syntax_highlighter_theme' => 'Default',
    'code_syntax_highlighter_toolbar' => 1,
    'code_syntax_highlighter_gutter' => 1,
    'code_syntax_highlighter_auto_links' => 1,
    'code_syntax_highlighter_smart_tabs' => 1,
    'code_syntax_highlighter_tab_size' => 4,
    'code_syntax_highlighter_pad_line_numbers' => 0,
);

// Update mod settings if applicable
foreach ($mod_settings as $new_setting => $new_value) {
    if (!isset($modSettings[$new_setting]))
        updateSettings(array($new_setting => $new_value));
}

<?php
/**
 * @package SMF Code Syntax Highlighter Mod
 * @author digger http://mysmf.ru
 * @copyright 2015
 * @license MIT http://opensource.org/licenses/mit-license.php
 * @version 2.0
 */

// TODO: field for lang select
// TODO: load when needed only

if (!defined('SMF'))
    die('Hacking attempt...');


/**
 * Load all needed hooks
 */
function loadCodeSyntaxHighlighterHooks()
{
    add_integration_function('integrate_load_theme', 'loadCodeSyntaxHighlighterAssets', false);
    add_integration_function('integrate_menu_buttons', 'addCodeSyntaxHighlighterCopyright', false);
    add_integration_function('integrate_admin_areas', 'addCodeSyntaxHighlighterAdminArea', false);
    add_integration_function('integrate_modify_modifications', 'addCodeSyntaxHighlighterAdminAction', false);
    add_integration_function('integrate_bbc_codes', 'changeCodeSyntaxHighlighterTag', false);
    //add_integration_function('integrate_create_post', 'addCodeSyntaxHighlighterForPost', false);
}


/**
 * Adds mod copyright to the forum credit's page
 */
function addCodeSyntaxHighlighterCopyright()
{
    global $context;

    if ($context['current_action'] == 'credits')
        $context['copyrights']['mods'][] = '<a href="http://mysmf.ru/code-syntax-highlighter" target="_blank">CodeSyntaxHighlighter</a> &copy; 2010-2015, digger';
}


/**
 * Add admin area
 * @param $admin_areas
 */
function addCodeSyntaxHighlighterAdminArea(&$admin_areas)
{
    global $txt;
    loadLanguage('CodeSyntaxHighlighter/');

    $admin_areas['config']['areas']['modsettings']['subsections']['code_syntax_highlighter'] = array($txt['code_syntax_highlighter_title_menu']);
}


/**
 * Add admin area action
 * @param $subActions
 */
function addCodeSyntaxHighlighterAdminAction(&$subActions)
{
    $subActions['code_syntax_highlighter'] = 'addCodeSyntaxHighlighterAdminSettings';
}


/**
 * @param bool $return_config
 * @return array config vars
 */
function addCodeSyntaxHighlighterAdminSettings($return_config = false)
{
    global $txt, $scripturl, $context;
    loadLanguage('CodeSyntaxHighlighter/');

    $context['page_title'] = $txt['code_syntax_highlighter_title_menu'];
    $context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=code_syntax_highlighter';

    $config_vars = array(
        array('title', 'code_syntax_highlighter_title_settings'),
        array('select', 'code_syntax_highlighter_theme',
            array(
                'shThemeDefault' => 'Default',
                'shThemeDjango' => 'Django',
                'shThemeEclipse' => 'Eclipse',
                'shThemeEmacs' => 'Emacs',
                'shThemeFadeToGrey' => 'Fade To Grey',
                'shThemeMidnight' => 'Midnight',
                'shThemeRDark' => 'RDark',
            ),
        ),
        array('check', 'code_syntax_highlighter_toolbar'),
        array('check', 'code_syntax_highlighter_gutter'),
        array('check', 'code_syntax_highlighter_auto_links'),
        array('check', 'code_syntax_highlighter_smart_tabs'),
        array('int', 'code_syntax_highlighter_tab_size'),
        array('check', 'code_syntax_highlighter_pad_line_numbers'),
    );

    if ($return_config)
        return $config_vars;

    if (isset($_GET['save'])) {
        checkSession();
        saveDBSettings($config_vars);
        redirectexit('action=admin;area=modsettings;sa=code_syntax_highlighter');
    }

    prepareDBSettingContext($config_vars);
}


function loadCodeSyntaxHighlighterAssets()
{
    global $modSettings, $context, $settings;

    $context['insert_after_template'] .= '
                <script src="' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/XRegExp.js" type="text/javascript"></script>
                <script src="' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/shCore.js" type="text/javascript"></script>
                <script src="' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/shAutoloader.js" type="text/javascript"></script>
                <script type="text/javascript"><!-- // --><![CDATA[
                    SyntaxHighlighter.config.bloggerMode = true;
                    SyntaxHighlighter.config.stripBrs = true;
                    ' . (!empty($modSettings['code_syntax_highlighter_toolbar']) ? 'SyntaxHighlighter.defaults["toolbar"] = true;' : 'SyntaxHighlighter.defaults["toolbar"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_gutter']) ? 'SyntaxHighlighter.defaults["gutter"] = true;' : 'SyntaxHighlighter.defaults["gutter"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_auto_links']) ? 'SyntaxHighlighter.defaults["auto-links"] = true;' : 'SyntaxHighlighter.defaults["auto-links"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_pad_line_numbers']) ? 'SyntaxHighlighter.defaults["pad-line-numbers"] = true;' : 'SyntaxHighlighter.defaults["pad-line-numbers"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_smart_tabs']) ? 'SyntaxHighlighter.defaults["smart-tabs"] = true;' : 'SyntaxHighlighter.defaults["smart-tabs"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_tab_size']) ? 'SyntaxHighlighter.defaults["tab-size"] = ' . $modSettings['code_syntax_highlighter_tab_size'] . ';' : 'SyntaxHighlighter.defaults["tab-size"] = 4;') . '
                    SyntaxHighlighter.autoloader(
	                    "applescript			' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushAppleScript.js",
	                    "actionscript3 as3		' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushAS3.js",
                        "bash shell				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushBash.js",
	                    "coldfusion cf			' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushColdFusion.js",
                        "cpp c					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushCpp.js",
	                    "c# c-sharp csharp		' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushCSharp.js",
	                    "css					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushCss.js",
	                    "delphi pascal			' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushDelphi.js",
	                    "diff patch pas			' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushDiff.js",
	                    "erl erlang				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushErlang.js",
	                    "groovy					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushGroovy.js",
	                    "haxe hx				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushHaxe.js",
	                    "java					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushJava.js",
	                    "jfx javafx				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushJavaFX.js",
                        "js jscript javascript	' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushJScript.js",
	                    "perl pl				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushPerl.js",
	                    "php					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushPhp.js",
	                    "text plain				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushPlain.js",
	                    "py python				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushPython.js",
	                    "ruby rails ror rb		' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushRuby.js",
	                    "scala					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushScala.js",
	                    "sql					' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushSql.js",
	                    "vb vbnet				' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushVb.js",
	                    "xml xhtml xslt html	' . $settings['default_theme_url'] . '/scripts/CodeSyntaxHighlighter/' . 'shBrushXml.js"
                    );
                    SyntaxHighlighter.all();
	            // ]]></script>
    ';

    $context['html_headers'] .= '
    <link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/CodeSyntaxHighlighter/' . 'shCore.css" />
    <link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/CodeSyntaxHighlighter/' . $modSettings['code_syntax_highlighter_theme'] . '.css" />
    ';
}


/**
 * change default code tag
 * @param array $codes default BB-codes array
 */
function changeCodeSyntaxHighlighterTag(&$codes = array())
{
    global $txt;

    foreach ($codes as $codeId => $code) {
        if ($code['tag'] == 'code' && $code['type'] == 'unparsed_equals_content') {
            //unset($codes[$codeId]);
            $codes[$codeId] = array(
                'tag' => 'code',
                'type' => 'unparsed_equals_content',
                //'test' => '[A-Za-z0-9_,\-\s]+?\]',
                'content' => '<div class="codeheader">' . $txt['code'] . ': ($2) </div><pre name="code" class="brush: $2">$1</pre>',
                'validate' => create_function('&$tag, &$data, $disabled', '
                    if (!isset($disabled[\'code\']))
					    $data[0] = rtrim($data[0], "\n\r");
            	'),
                'block_level' => true,
                'disabled_content' => '<pre>$1</pre>',
            );
        }
        elseif ($code['tag'] == 'code' && $code['type'] == 'unparsed_content') {
            //unset($codes[$codeId]);
            $codes[$codeId] = array(
                'tag' => 'code',
                'type' => 'unparsed_content',
                'content' => '<div class="codeheader">' . $txt['code'] . ': (text) </div><pre name="code" class="brush: php">$1</pre>',
                'validate' => create_function('&$tag, &$data, $disabled', '
                    if (!isset($disabled[\'code\']))
					    $data = rtrim($data, "\n\r");
            	'),
                'block_level' => true,
                'disabled_content' => '<pre>$1</pre>',
            );
        }
        elseif ($code['tag'] == 'php' && $code['type'] == 'unparsed_content') {
            //unset($codes[$codeId]);
            $codes[$codeId] = array(
                'tag' => 'php',
                'type' => 'unparsed_content',
                'content' => '<div class="codeheader">' . $txt['code'] . ': (php) </div><pre name="code" class="brush: php">$1</pre>',
                'validate' => create_function('&$tag, &$data, $disabled', '
                    if (!isset($disabled[\'php\']))
					    $data = rtrim($data, "\n\r");
            	'),
                'block_level' => true,
                'disabled_content' => '<pre>$1</pre>',
            );
        }
    }
}
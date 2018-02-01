<?php
/**
 * @package SMF Code Syntax Highlighter Mod
 * @author digger http://mysmf.net
 * @copyright 2012-2018
 * @license The MIT License (MIT)
 * @version 2.0
 */

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
}


/**
 * Adds mod copyright to the forum credit's page
 */
function addCodeSyntaxHighlighterCopyright()
{
    global $context;

    if ($context['current_action'] == 'credits')
        $context['copyrights']['mods'][] = '<a href="http://mysmf.net/mods/code-syntax-highlighter" target="_blank">CodeSyntaxHighlighter</a> &copy; 2012-2018, digger';
}


/**
 * Add admin area
 * @param $admin_areas
 */
function addCodeSyntaxHighlighterAdminArea(&$admin_areas)
{
    global $txt;
    loadLanguage('CodeSyntaxHighlighter/CodeSyntaxHighlighter');

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
    loadLanguage('CodeSyntaxHighlighter/CodeSyntaxHighlighter');

    $context['page_title'] = $txt['code_syntax_highlighter_title_menu'];
    $context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=code_syntax_highlighter';

    $config_vars = array(
        array('title', 'code_syntax_highlighter_title_settings'),
        array('select', 'code_syntax_highlighter_engine',
            array(
                'sh' => 'SyntaxHighlighter',
                'hljs' => 'Highlight.js',
            ),
        ),
        array('select', 'code_syntax_highlighter_font_size',
            array(
                'small' => $txt['code_syntax_highlighter_font_small'],
                'medium' => $txt['code_syntax_highlighter_font_medium'],
                'large' => $txt['code_syntax_highlighter_font_large'],
            ),
        ),


        array('title', 'code_syntax_highlighter_title_syntax_highlighter'),
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
        array('check', 'code_syntax_highlighter_gutter'),
        array('check', 'code_syntax_highlighter_pad_line_numbers'),
        array('check', 'code_syntax_highlighter_auto_links'),
        array('check', 'code_syntax_highlighter_smart_tabs'),

        array('title', 'code_syntax_highlighter_title_hljs'),
        array('select', 'code_syntax_highlighter_hljs_load',
            array(
                'local' => 'Локально',
                'cloudflare' => 'Cloudflare CDN',
                'jsdelivr' => ' jsDelivr CDN',
            )
        ),
        array('select', 'code_syntax_highlighter_hljs_theme',
            array(
                'default' => 'Default',
                'agate' => 'Agate',
                'androidstudio' => 'Android Studio',
                'arta' => 'Arta',
                'ascetic' => 'Ascetic',
                'atelier-dune.dark' => 'Atelier Dune - Dark',
                'atelier-dune.light' => 'Atelier Dune - Light',
                'brown_paper' => 'Brown Paper',
                'codepen-embed' => 'Codepen.io Embed',
                'color-brewer' => 'Colorbrewer',
                'dark' => 'Dark',
                'darkula' => 'Darkula',
                'docco' => 'Docco',
                'far' => 'FAR',
                'foundation' => 'Foundation',
                'github' => 'GitHub',
                'googlecode' => 'Google Code',
                'hybrid' => 'Hybrid',
                'idea' => 'IDEA',
                //
                'xcode' => 'XCode',
                'zenburn' => 'Zenburn',
            ),
        ),
    );

/*
IR Black
Kimbie - Dark
Kimbie - Light
Magula
Mono Blue
Monokai
Monokai Sublime
Obsidian
Paraíso - Dark
Paraíso - Light
Pojoaque
Railscasts
Rainbow
School Book
Solarized - Dark
Solarized - Light
Sunburst
Tomorrow
Tomorrow Night
Tomorrow Night Blue
Tomorrow Night Bright
Tomorrow Night Eighties
*/

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

    if ($modSettings['code_syntax_highlighter_engine'] == 'sh') {
        // Load Syntax Highlighter
        $context['insert_after_template'] .= '
                <script src="' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/XRegExp.js" type="text/javascript"></script>
                <script src="' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/shCore.js" type="text/javascript"></script>
                <script src="' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/shAutoloader.js" type="text/javascript"></script>
                <script type="text/javascript"><!-- // --><![CDATA[
                    SyntaxHighlighter.config.bloggerMode = true;
                    SyntaxHighlighter.config.stripBrs = true;
                    SyntaxHighlighter.defaults["toolbar"] = false;
                    SyntaxHighlighter.defaults["tab-size"] = 4;
                    ' . (!empty($modSettings['code_syntax_highlighter_gutter']) ? 'SyntaxHighlighter.defaults["gutter"] = true;' : 'SyntaxHighlighter.defaults["gutter"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_auto_links']) ? 'SyntaxHighlighter.defaults["auto-links"] = true;' : 'SyntaxHighlighter.defaults["auto-links"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_pad_line_numbers']) ? 'SyntaxHighlighter.defaults["pad-line-numbers"] = true;' : 'SyntaxHighlighter.defaults["pad-line-numbers"] = false;') . '
                    ' . (!empty($modSettings['code_syntax_highlighter_smart_tabs']) ? 'SyntaxHighlighter.defaults["smart-tabs"] = true;' : 'SyntaxHighlighter.defaults["smart-tabs"] = false;') . '
                    SyntaxHighlighter.autoloader(
	                    "applescript			' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushAppleScript.js",
	                    "actionscript3 as3		' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushAS3.js",
                        "bash shell				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushBash.js",
	                    "coldfusion cf			' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushColdFusion.js",
                        "cpp c					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushCpp.js",
	                    "c# c-sharp csharp		' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushCSharp.js",
	                    "css					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushCss.js",
	                    "delphi pascal			' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushDelphi.js",
	                    "diff patch pas			' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushDiff.js",
	                    "erl erlang				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushErlang.js",
	                    "groovy					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushGroovy.js",
	                    "haxe hx				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushHaxe.js",
	                    "java					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushJava.js",
	                    "jfx javafx				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushJavaFX.js",
                        "js jscript javascript	' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushJScript.js",
	                    "perl pl				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushPerl.js",
	                    "php					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushPhp.js",
	                    "text plain				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushPlain.js",
	                    "py python				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushPython.js",
	                    "ruby rails ror rb		' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushRuby.js",
	                    "scala					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushScala.js",
	                    "sql					' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushSql.js",
	                    "vb vbnet				' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushVb.js",
	                    "xml xhtml xslt html	' . $settings['default_theme_url'] . '/scripts/SyntaxHighlighter/' . 'shBrushXml.js"
                    );
                    SyntaxHighlighter.all();
	            // ]]></script>
    ';

        $context['html_headers'] .= '
    <link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/SyntaxHighlighter/' . 'shCore.css" />
    <link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/SyntaxHighlighter/' . $modSettings['code_syntax_highlighter_theme'] . '.css" />
    ';
    } elseif ($modSettings['code_syntax_highlighter_engine'] == 'hljs') {

        // Load highlight.js
        switch ($modSettings['code_syntax_highlighter_hljs_load']) {

            case 'cloudflare';
                $context['html_headers'] .= '
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/styles/' . $modSettings['code_syntax_highlighter_hljs_theme'] . '.min.css /">';
                $context['insert_after_template'] .= '
                <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/highlight.min.js" type="text/javascript"></script>';
                break;

            case 'jsdelivr';
                $context['html_headers'] .= '
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/highlight.js/9.5.0/styles/' . $modSettings['code_syntax_highlighter_hljs_theme'] . '.min.css" />';
                $context['insert_after_template'] .= '
                <script src="//cdn.jsdelivr.net/highlight.js/9.5.0/highlight.min.js" type="text/javascript"></script>';
                break;

            case 'local';
                $i = 1;
                break;
        }

        $context['insert_after_template'] .= '
                <script type="text/javascript"><!-- // --><![CDATA[
                    hljs.initHighlightingOnLoad();
	            // ]]></script>';

    } else return false;

    return true;
}


/**
 * change default code tag
 * @param array $codes default BB-codes array
 */
function changeCodeSyntaxHighlighterTag(&$codes = array())
{
    global $modSettings, $txt;

    foreach ($codes as $codeId => $code) {
        if ($code['tag'] == 'code' && $code['type'] == 'unparsed_equals_content') {
            $codes[$codeId] = array(
                'tag' => 'code',
                'type' => 'unparsed_equals_content',
                'content' => '<div class="codeheader">' . $txt['code'] . ': ($2) </div><div style="font-size: ' . $modSettings['code_syntax_highlighter_font_size'] . '">' .
                    ($modSettings['code_syntax_highlighter_engine'] == 'hljs' ? '<pre name="code"><code class="$2">$1</code></pre>' :
                        ($modSettings['code_syntax_highlighter_engine'] == 'sh' ? '<pre name="code" class="brush: $2">$1</pre>' : '<pre>$1</pre>')) . '</div>',
                'validate' => create_function('&$tag, &$data, $disabled', '
                    if (!isset($disabled[\'code\']))
					    $data[0] = rtrim($data[0], "\n\r");
            	'),
                'block_level' => true,
                'disabled_content' => '<pre>$1</pre>',
            );
        } elseif ($code['tag'] == 'code' && $code['type'] == 'unparsed_content') {
            $codes[$codeId] = array(
                'tag' => 'code',
                'type' => 'unparsed_content',
                'content' => '<div class="codeheader">' . $txt['code'] . '</div><div style="font-size: ' . $modSettings['code_syntax_highlighter_font_size'] . '">' .
                    ($modSettings['code_syntax_highlighter_engine'] == 'hljs' ? '<pre name="code"><code>$1</code></pre>' :
                        ($modSettings['code_syntax_highlighter_engine'] == 'sh' ? '<pre name="code" class="brush: text">$1</pre>' : '<pre>$1</pre>')) . '</div>',
                'validate' => create_function('&$tag, &$data, $disabled', '
                    if (!isset($disabled[\'code\']))
					    $data = rtrim($data, "\n\r");
            	'),
                'block_level' => true,
                'disabled_content' => '<pre>$1</pre>',
            );
        } elseif ($code['tag'] == 'php' && $code['type'] == 'unparsed_content') {
            $codes[$codeId] = array(
                'tag' => 'php',
                'type' => 'unparsed_content',
                'content' => '<div class="codeheader">' . $txt['code'] . ': (php) </div><div style="font-size: ' . $modSettings['code_syntax_highlighter_font_size'] . '">' .
                    ($modSettings['code_syntax_highlighter_engine'] == 'hljs' ? '<pre name="code"><code class="php">$1</code></pre>' :
                        ($modSettings['code_syntax_highlighter_engine'] == 'sh' ? '<pre name="code" class="brush: php">$1</pre>' : '<pre>$1</pre>')) . '</div>',

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

<?php

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_css_class_replacer';
$GLOBALS['BE_MOD']['design']['themes']['exportCssClassReplacerRules'] = array(
    'Toflar\Contao\CssClassReplacer\XmlExporter',
    'export'
);
$GLOBALS['BE_MOD']['design']['themes']['importCssClassReplacerRules'] = array(
    'Toflar\Contao\CssClassReplacer\XmlImporter',
    'import'
);

/**
 * Events
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Toflar\Contao\CssClassReplacer\AddRulesSubscriber';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_css_class_replacer'] = 'Toflar\Contao\CssClassReplacer\RuleModel';

/**
 * Maintenance
 */
$GLOBALS['TL_MAINTENANCE'][] = 'Toflar\Contao\CssClassReplacer\Maintenance\RuleUpdate';

/**
 * easy_themes support
 */
$GLOBALS['TL_EASY_THEMES_MODULES']['css-class-replacer'] = array(
    'href_fragment' => 'table=tl_css_class_replacer',
    'icon'          => 'system/modules/css-class-replacer/assets/css-class-replacer.png'
);
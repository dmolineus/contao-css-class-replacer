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
 * Hooks
 */
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array(
    'Toflar\Contao\CssClassReplacer\Listener',
    'replaceCssClasses'
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_css_class_replacer'] = 'Toflar\Contao\CssClassReplacer\RuleModel';

/**
 * Maintenance
 */
$GLOBALS['TL_MAINTENANCE'][] = 'Toflar\Contao\CssClassReplacer\Maintenance\RuleUpdate';

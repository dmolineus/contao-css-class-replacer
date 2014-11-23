<?php

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['design']['themes']['tables'][] = 'tl_css_class_replacer';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array(
    'Toflar\Contao\CssClassReplacer\Listener',
    'replaceCssClasses'
);
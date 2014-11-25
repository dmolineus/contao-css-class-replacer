<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_css_class_replacer']['type']        = array('Selector Type', 'Choose whether you want to define a selector rule using a CSS selector or an XPath expression.');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['selector']    = array('Selector', 'Enter a selector to fetch the elements you want to modify.');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['replacement'] = array('Replacement', 'Enter the CSS class you would like to have. You can use the tokens ##all## for all existing CSS classes and each of them by index: ##class_0##, ##class_1## etc.');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['published']   = array('Publish rule', 'Apply the rule for this theme.');

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_css_class_replacer']['new']                       = array('New rule', 'Create a new rule');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['edit']                      = array('Edit rule', 'Edit rule ID %s');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['copy']                      = array('Copy rule', 'Copy rule ID %s');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['delete']                    = array('Delete rule', 'Delete rule ID %s');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['toggle']                    = array('Publish/Unpublish rule', 'Publish/Unpublish rule ID %s');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['show']                      = array('Rule details', 'Show details of rule ID %s');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['import']                    = array('Import rules', 'Imports rules from an XML file');
$GLOBALS['TL_LANG']['tl_css_class_replacer']['export']                    = array('Export rules', 'Exports all the rules as XML file');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_css_class_replacer']['title_legend']    = 'Selector & Replacement';

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_css_class_replacer']['type']['css']    = 'CSS';
$GLOBALS['TL_LANG']['tl_css_class_replacer']['type']['xpath']  = 'XPath';

/**
 * Import
 */
$GLOBALS['TL_LANG']['tl_css_class_replacer']['source'] = array('Source', 'Choose one or more XML files containing the rules here.');
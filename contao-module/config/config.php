<?php

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array(
    'Toflar\Contao\CssClassReplacer\Listener',
    'replaceCssClasses'
);
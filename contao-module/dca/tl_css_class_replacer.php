<?php


/**
 * Table tl_css_class_replacer
 */
$GLOBALS['TL_DCA']['tl_css_class_replacer'] = array
(
    // Config
    'config' => array
    (
        'ptable'                      => 'tl_theme',
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id'  => 'primary',
                'pid' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'fields'                  => array('sorting'),
            'headerFields'            => array('name', 'author'),
            'panelLayout'             => 'filter,sort;search,limit',
            'child_record_callback'   => array('Toflar\Contao\CssClassReplacer\BackendHelper', 'generateRow')
        ),
        'label' => array
        (
            'fields'                  => array('selector'),
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ),
            'import' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['import'],
                'href'                => 'key=importCssClassReplacerRules',
                'icon'                => 'system/modules/css-class-replacer/assets/import.png'
            ),
            'export' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['export'],
                'href'                => 'key=exportCssClassReplacerRules',
                'icon'                => 'system/modules/css-class-replacer/assets/export.png'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'copy' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif'
            ),
            'cut' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['cut'],
                'href'                => 'act=paste&amp;mode=cut',
                'icon'                => 'cut.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset()"'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('Toflar\Contao\CssClassReplacer\BackendHelper', 'toggleIcon')
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default'                     => '{title_legend},type,selector,replacement,published',
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'sorting' => array
        (
            'sorting'                 => true,
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'type' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['type'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'options'                 => array('css', 'xpath'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['type'],
            'eval'                    => array('mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'clr'),
            'sql'                     => "varchar(8) NOT NULL default ''"
        ),
        'selector' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['selector'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'textarea',
            'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'clr'),
            'sql'                     => "text NULL",
            'save_callback'           => array(
                function($value, $dc) {
                    if ($dc->activeRecord->type === 'css') {
                        // throws an exception if not supported
                        \Symfony\Component\CssSelector\CssSelector::toXPath($value);
                    }

                    return $value;
                }
            )
        ),
        'replacement' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['replacement'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'textarea',
            'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'clr'),
            'sql'                     => "text NULL"
        ),
        'published' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['published'],
            'exclude'                 => true,
            'filter'                  => true,
            'flag'                    => 1,
            'inputType'               => 'checkbox',
            'eval'                    => array('doNotCopy'=>true, 'tl_class'=>'clr'),
            'sql'                     => "char(1) NOT NULL default ''"
        )
    )
);

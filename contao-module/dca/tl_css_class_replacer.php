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
            'mode'                    => 1,
            'fields'                  => array('selector'),
            'flag'                    => 1,
            'panelLayout'             => 'filter;search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('selector'),
            //'label_callback'          => array('x', 'x')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
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
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),/*
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_css_class_replacer']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('x', 'x')
            ),*/
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
        'default'                     => '{title_legend},type,selector,replacement',
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
            'inputType'               => 'textarea',
            'eval'                    => array('mandatory'=>true, 'decodeEntities'=>true, 'tl_class'=>'clr'),
            'sql'                     => "text NULL",
            'save_callback'           => array(
                function($value, $dc) {
                    if (($model = \Toflar\Contao\CssClassReplacer\Model::findByPk($dc->id)) !== null) {
                        $model->getXPathExpr();
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
        )
    )
);
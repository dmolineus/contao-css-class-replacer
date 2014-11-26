<?php

namespace Toflar\Contao\CssClassReplacer;


class BackendHelper
{
    /**
     * Return the "toggle visibility" button
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(\Input::get('tid'))) {
            $this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1), (@func_get_arg(12) ?: null));
            \Backend::redirect(\Controller::getReferer());
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.gif';
        }

        return '<a href="'.\Backend::addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ';
    }


    /**
     * Publish/unpublish rule
     * @param integer
     * @param boolean
     * @param \DataContainer
     */
    public function toggleVisibility($intId, $blnVisible, \DataContainer $dc = null)
    {
        $objVersions = new \Versions('tl_css_class_replacer', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_css_class_replacer']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_css_class_replacer']['fields']['published']['save_callback'] as $callback) {
                if (is_array($callback)) {
                    $blnVisible = \System::importStatic($callback[0])->$callback[1]($blnVisible, ($dc ?: $this));
                } elseif (is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, ($dc ?: $this));
                }
            }
        }

        // Update the database
        \Database::getInstance()->prepare("UPDATE tl_css_class_replacer SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
            ->execute($intId);

        $objVersions->create();
        \System::log('A new version of record "tl_css_class_replacer.id='.$intId.'" has been created', __METHOD__, TL_GENERAL);
    }

    /**
     * Generate the child record row.
     *
     * @param array $row Current row.
     *
     * @return string
     */
    public function generateRow($row)
    {
        return sprintf('%s <span class="tl_gray">[%s]</span>',
            $row['selector'],
            $GLOBALS['TL_LANG']['tl_css_class_replacer']['type'][$row['type']]
        );
    }
} 

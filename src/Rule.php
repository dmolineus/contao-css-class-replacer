<?php

namespace Toflar\Contao\CssClassReplacer;

use Symfony\Component\CssSelector\CssSelector;

class Rule extends \Model
{
    /**
     * Table name
     * @var string
     */
    static $strTable = 'tl_css_class_replacer';

    /**
     * Get XPath expression
     *
     * @return string
     * @throws \Exception
     */
    public function getXPathExpr()
    {
        if ($this->type === 'css') {
            return CssSelector::toXPath($this->selector);
        }

        return $this->selector;
    }


    /**
     * Find by theme id
     *
     * @param int
     * @param array
     * @return static
     */
    public static function findPublishedByThemeId($id, array $arrOptions=array())
    {
        $t = static::$strTable;
        $arrColumns = array("$t.pid=?");

        if (!BE_USER_LOGGED_IN) {
            $arrColumns[] = "$t.published=1";
        }

        return static::findBy($arrColumns, $id, $arrOptions);
    }


    /**
     * Find by currently active theme
     *
     * @param array
     * @return static
     */
    public static function findPublishedByCurrentlyActiveTheme(array $arrOptions=array())
    {
        global $objPage;

        if (($layout = \LayoutModel::findByPk($objPage->layout)) === null) {
            return null;
        }

        return static::findPublishedByThemeId($layout->pid, $arrOptions);
    }
}

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
        return $this->xpath_expression;
    }

    /**
     * Create and update XPath expression
     */
    public function updateXPathExpression()
    {
        if ($this->type === 'css') {
            $this->xpath_expression = CssSelector::toXPath($this->selector);
        } else {
            $this->xpath_expression = $this->selector;
        }
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

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = 'sorting';
        }

        return static::findBy($arrColumns, $id, $arrOptions);
    }

    /**
     * Find by active theme
     *
     * @param array
     * @return static
     */
    public static function findPublishedByActiveTheme(array $arrOptions=array())
    {
        global $objPage;

        if (($layout = \LayoutModel::findByPk($objPage->layout)) === null) {
            return null;
        }

        return static::findPublishedByThemeId($layout->pid, $arrOptions);
    }
}

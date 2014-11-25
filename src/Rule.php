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
    public static function findByThemeId($id, array $arrOptions=array())
    {
        return static::findBy('pid', $id, $arrOptions);
    }


    /**
     * Find by currently active theme
     *
     * @param array
     * @return static
     */
    public static function findByCurrentlyActiveTheme(array $arrOptions=array())
    {
        global $objPage;

        if (($layout = \LayoutModel::findByPk($objPage->layout)) === null) {
            return null;
        }

        return static::findByThemeId($layout->pid, $arrOptions);
    }
} 
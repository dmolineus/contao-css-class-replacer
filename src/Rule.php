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
     * @return static
     */
    public static function findByThemeId($id)
    {
        return static::findBy('pid', $id);
    }


    /**
     * Find by currently active theme
     *
     * @return static
     */
    public static function findByCurrentlyActiveTheme()
    {
        global $objPage;

        if (($layout = \LayoutModel::findByPk($objPage->layout)) === null) {
            return null;
        }

        return static::findByThemeId($layout->pid);
    }
} 
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
} 
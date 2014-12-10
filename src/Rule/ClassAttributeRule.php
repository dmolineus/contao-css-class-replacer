<?php

namespace Toflar\Contao\CssClassReplacer\Rule;

class ClassAttributeRule extends AbstractRule
{
    /**
     * Apply Rule filters.
     *
     * @param \DomElement $node
     *
     * @return mixed
     */
    public function apply(\DomElement $node)
    {
        $attr       = $node->attributes;
        $classNode  = $attr->getNamedItem('class');
        $classValue = $this->applyFilter($classNode ? $classNode->nodeValue : '');

        // Replace if it already exists
        if ($classNode) {
            $classNode->nodeValue = $classValue;
        } else {
            // Otherwise add
            $node->setAttribute('class', $classValue);
        }
    }

    /**
     * Apply defined filters.
     *
     * @param string $cssClass Css class value.
     *
     * @return string
     */
    private function applyFilter($cssClass)
    {
        foreach ($this->getFilters() as $filter) {
            $cssClass = $filter($cssClass);
        }

        // Clean output
        return trim(preg_replace('/\s+/',  ' ', $cssClass));
    }
}

<?php

namespace Toflar\Contao\CssClassReplacer;


use Netzmacht\DomManipulator\Filter\ValueFilterInterface;

class CssClassFilter implements ValueFilterInterface
{
    /**
     * Directives
     *
     * @var array
     */
    private $directives = array();

    /**
     * Create a CssClassFilter instance
     *
     * @param array $directives
     */
    function __construct(array $directives)
    {
        $this->directives = $directives;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        if ($value !== '') {
            // Apply simple replacement directives
            if (!empty($this->directives['replace_search'])) {
                $value = str_replace($this->directives['replace_search'],
                    $this->directives['replace_values'],
                    $value);
            }

            // Apply rgxp replacement directives
            if (!empty($this->directives['rgxp_replace_search'])) {
                $value = preg_replace($this->directives['rgxp_replace_search'],
                    $this->directives['rgxp_replace_values'],
                    $value);
            }
        }

        // Apply add directives
        if (!empty($this->directives['add'])) {
            $value .= ' ' . implode(' ', $this->directives['add']);
        }

        return $value;
    }
} 
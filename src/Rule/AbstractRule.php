<?php

namespace Toflar\Contao\CssClassReplacer\Rule;

use Toflar\Contao\CssClassReplacer\RuleInterface;

abstract class AbstractRule implements RuleInterface
{
    /**
     * Filters which should be applied.
     *
     * @var \Callable[]
     */
    private $filters = array();

    /**
     * XPath expression.
     *
     * @var string
     */
    private $xPathExpr;

    /**
     * Construct.
     *
     * @param string $xPathExpr XPath expression.
     */
    public function __construct($xPathExpr = null)
    {
        $this->xPathExpr = $xPathExpr;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearFilters()
    {
        $this->filters = array();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function setXPathExpr($expr)
    {
        $this->xPathExpr = $expr;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getXPathExpr()
    {
        return $this->xPathExpr;
    }
}

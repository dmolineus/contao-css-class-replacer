<?php

namespace Toflar\Contao\CssClassReplacer;

interface RuleInterface
{
    /**
     * Add a filter which will be applied.
     *
     * @param \Callable $filter Filter are callables. The passed arguments depends on the Rule implementation.
     *
     * @return $this
     */
    public function addFilter($filter);

    /**
     * Remove all filters.
     *
     * @return $this
     */
    public function clearFilter();

    /**
     * Get all filters.
     *
     * @return \Callable[]
     */
    public function getFilters();

    /**
     * Set XPath expression.
     *
     * @param string $expr XPath Expression.
     *
     * @return $this
     */
    public function setXPathExpr($expr);

    /**
     * Get XPath expression.
     *
     * @return string
     */
    public function getXPathExpr();

    /**
     * Apply Rule filters.
     *
     * @param \DomElement $node
     *
     * @return mixed
     */
    public function apply(\DomElement $node);
}

<?php

namespace Toflar\Contao\CssClassReplacer;

class DomManipulator
{
    /**
     * Rules.
     *
     * @var array|RuleInterface[]
     */
    private $rules = array();

    /**
     * Dom document.
     *
     * @var \DOMDocument
     */
    private $document;

    /**
     * Construct.
     *
     * @param string          $encoding Charset encoding.
     * @param RuleInterface[] $rules    Rules.
     */
    public function __construct($encoding, array $rules = array())
    {
        $this->rules    = $rules;
        $this->document = new \DOMDocument('1,1', $encoding);
        $this->document->strictErrorChecking = false;
    }

    /**
     * Add rules to manipulator.
     *
     * @param RuleInterface[] $rules Rules.
     *
     * @return $this
     */
    public function addRules(array $rules)
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    /**
     * Load html into the manipulator.
     *
     * @param string $buffer  HTML buffer.
     * @param string $charset Charset of the html.
     *
     * @return $this
     */
    public function loadHtml($buffer, $charset = null)
    {
        // Tell the parser which charset to use
        $charset  = $charset ?: $this->document->encoding;
        $encoding = '<?xml encoding="' . $charset . '" ?>';
        $buffer   = $encoding . $buffer;

        @$this->document->loadHTML($buffer);

        foreach ($this->document->childNodes as $item) {
            if ($item->nodeType == XML_PI_NODE) {
                $this->document->removeChild($item);
            }
        }

        return $this;
    }

    /**
     * Manipulate document.
     *
     * @return string
     */
    public function manipulate()
    {
        $xPath = new \DOMXPath($this->document);

        foreach ($this->rules as $rule) {
            $nodeList = $xPath->query($rule->getXPathExpr());

            foreach ($nodeList as $node) {
                $rule->apply($node);
            }
        }

        return $this->document->saveHTML();
    }
}

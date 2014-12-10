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
     * Silent mode will ignore exceptions caused by broken rules.
     *
     * @var bool
     */
    private $silentMode;

    /**
     * Construct.
     *
     * @param string          $encoding   Charset encoding.
     * @param RuleInterface[] $rules      Rules.
     * @param bool            $silentMode Set silent mode.
     */
    public function __construct($encoding, array $rules = array(), $silentMode = false)
    {
        $this->silentMode = $silentMode;
        $this->rules      = $rules;

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
     * Check if manipulator is in silent mode.
     *
     * @return bool
     */
    public function isSilentMode()
    {
        return $this->silentMode;
    }

    /**
     * Set silent mode.
     *
     * @param bool $silentMode Silent mode.
     *
     * @return $this
     */
    public function setSilentMode($silentMode)
    {
        $this->silentMode = $silentMode;

        return $this;
    }

    /**
     * Manipulate document.
     *
     * @throws \Exception If a broken rule is executed and silent mode is not enabled.
     *
     * @return string
     */
    public function manipulate()
    {
        $xPath = new \DOMXPath($this->document);

        foreach ($this->rules as $rule) {
            try {
                $nodeList = $xPath->query($rule->getXPathExpr());

                foreach ($nodeList as $node) {
                    $rule->apply($node);
                }
            } catch (\Exception $e) {
                if (!$this->silentMode) {
                    throw $e;
                }
            }
        }

        return $this->document->saveHTML();
    }
}

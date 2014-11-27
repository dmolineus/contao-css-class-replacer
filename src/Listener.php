<?php

namespace Toflar\Contao\CssClassReplacer;


use Symfony\Component\Stopwatch\Stopwatch;

class Listener extends \Controller
{
    /**
     * @var \DOMDocument
     */
    private $doc;

    /**
     * Replace CSS classes
     *
     * @param $buffer
     * @return string
     */
    public function replaceCssClasses($buffer)
    {
        if (($rules = Rule::findPublishedByActiveTheme()) === null) {
            return $buffer;
        }

        $stopWatch = new Stopwatch();
        $stopWatch->start('css_class_replacer');

        // Replace insert tags first because DOMDocument will encode them
        $buffer = $this->replaceInsertTags($buffer);

        $this->doc = new \DOMDocument('1.1', 'UTF-8');
        $this->doc->strictErrorChecking = false;
        @$this->doc->loadHTML($buffer);
        $xPath = new \DOMXPath($this->doc);

        /**
         * @var $rule \Toflar\Contao\CssClassReplacer\Rule
         */
        foreach ($rules as $rule) {
            try {
                $nodeList = $xPath->query($rule->getXPathExpr());

                foreach ($nodeList as $node) {
                    $this->modifyNode($node, $rule);
                }

            } catch (\Exception $e) {}
        }

        $this->addTimeToDebugBar($stopWatch);

        return $this->doc->saveHTML();
    }

    private function modifyNode(\DOMElement $node, Rule $rule)
    {
        $attr = $node->attributes;
        $classNode = $attr->getNamedItem('class');

        // Replace if it already exists
        if ($classNode) {
            $classNode->nodeValue = $rule->applyRulesOnClass($classNode->nodeValue);
        } else {
            // Otherwise append
            $node->setAttribute('class', $rule->applyRulesOnClass($classNode->nodeValue));
        }
    }

    private function addTimeToDebugBar(Stopwatch $stopWatch)
    {
        if (!$GLOBALS['TL_CONFIG']['debugMode']) {
            return;
        }

        $stopEvent = $stopWatch->stop('css_class_replacer');

        $GLOBALS['TL_DEBUG']['css-class-replacer'] = 'CSS replacements time: ' . $stopEvent->getDuration() . ' ms';
    }
}
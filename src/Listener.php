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
        if (($rules = RuleModel::findPublishedByActiveTheme()) === null) {
            return $buffer;
        }

        $stopWatch = new Stopwatch();
        $stopWatch->start('css_class_replacer');

        $this->doc = new \DOMDocument('1.1', $GLOBALS['TL_CONFIG']['characterSet']);
        $this->doc->strictErrorChecking = false;
        $this->loadHtml($buffer);

        $xPath = new \DOMXPath($this->doc);

        /**
         * @var $rule \Toflar\Contao\CssClassReplacer\RuleModel
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

    private function modifyNode(\DOMElement $node, RuleModel $rule)
    {
        $attr = $node->attributes;
        $classNode = $attr->getNamedItem('class');

        // Replace if it already exists
        if ($classNode) {
            $classNode->nodeValue = $rule->applyRulesOnClass($classNode->nodeValue);
        } else {
            // Otherwise add
            $node->setAttribute('class', $rule->applyRulesOnClass(''));
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

    /**
     * Load html from buffer and take care of correct encoding
     *
     * @param $buffer
     */
    private function loadHtml($buffer)
    {
        // Tell the parser which charset being used
        $encoding = '<?xml encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '" ?>';
        $buffer   = $encoding . $buffer;

        @$this->doc->loadHTML($buffer);

        foreach ($this->doc->childNodes as $item) {
            if ($item->nodeType == XML_PI_NODE) {
                $this->doc->removeChild($item);
            }
        }
    }
}

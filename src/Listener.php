<?php

namespace Toflar\Contao\CssClassReplacer;


class Listener
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

        $start = microtime(true);

        $this->doc = new \DOMDocument();
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

        $this->addTimeToDebugBar($start);

        return $this->doc->saveHTML();
    }

    private function modifyNode(\DOMElement $node, Rule $rule)
    {
        $attr = $node->attributes;
        $classNode = $attr->getNamedItem('class');

        // Replace if it already exists
        if ($classNode) {
            $replacement = \String::parseSimpleTokens(
                $rule->replacement,
                $this->createTokensFromClassString($classNode->nodeValue)
            );

            $classNode->nodeValue = $replacement;
        } else {
            // Otherwise append
            $node->setAttribute('class', \String::parseSimpleTokens(
                $rule->replacement,
                $this->createTokensFromClassString('')
            ));
        }
    }

    private function createTokensFromClassString($classString)
    {
        $tokens = array(
            'all'   => $classString
        );

        if ($classString === '') {
            return $tokens;
        }

        $chunks = preg_split('/ +/', $classString, -1, PREG_SPLIT_NO_EMPTY);
        $i = 1;

        foreach ($chunks as $class) {
            $tokens['class_' . $i] = $class;
            $i++;
        }

        return $tokens;
    }

    private function addTimeToDebugBar($start)
    {
        if (!$GLOBALS['TL_CONFIG']['debugMode']) {
            return;
        }

        $elapsed = (microtime(true) - $start);
        $ms = \System::getFormattedNumber(($elapsed * 1000), 0);

        $GLOBALS['TL_DEBUG']['css-class-replacer'] = 'CSS replacements time: ' . $ms . ' ms';
    }
}
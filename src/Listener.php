<?php

namespace Toflar\Contao\CssClassReplacer;


class Listener
{
    public function replaceCssClasses($buffer)
    {
        if (($rules = Rule::findPublishedByCurrentlyActiveTheme()) === null) {
            return $buffer;
        }

        $start = microtime(true);

        $doc = new \DOMDocument();
        $doc->strictErrorChecking = false;
        @$doc->loadHTML($buffer);
        $xPath = new \DOMXPath($doc);

        /**
         * @var $rule \Toflar\Contao\CssClassReplacer\Rule
         */
        foreach ($rules as $rule) {
            try {
                $nodeList = $xPath->evaluate($rule->getXPathExpr());

                foreach ($nodeList as $node) {
                    $this->modifyNode($node, $rule);
                }

            } catch (\Exception $e) {}
        }

        $this->addTimeToDebugBar($start);

        return $doc->saveHTML();
    }

    private function modifyNode(\DOMNode $node, Rule $rule)
    {
        $attr = $node->attributes;
        $classNode = $attr->getNamedItem('class');

        if ($classNode) {
            $replacement = \String::parseSimpleTokens(
                $rule->replacement,
                $this->createTokensFromClassString($classNode->nodeValue)
            );

            $classNode->nodeValue = $replacement;
        }
    }

    private function createTokensFromClassString($classString)
    {
        $tokens = array(
            'all'   => $classString
        );

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
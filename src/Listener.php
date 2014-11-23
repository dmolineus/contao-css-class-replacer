<?php

namespace Toflar\Contao\CssClassReplacer;


class Listener
{
    public function replaceCssClasses($buffer)
    {
        if (($rules = Rule::findAll()) === null) {
            return $buffer;
        }

        $doc = new \DOMDocument();
        $doc->loadHTML($buffer);
        $xPath = new \DOMXPath($doc);

        /**
         * @var $rule \Toflar\Contao\CssClassReplacer\Rule
         */
        foreach ($rules as $rule) {
            try {
                $nodeList = $xPath->evaluate($rule->getXPathExpr());
                foreach ($nodeList as $node) {
                    $this->modifyNode($node);
                }

            } catch (\Exception $e) {}
        }

        return $doc->saveHTML();
    }

    private function modifyNode(\DOMNode $node)
    {
        $attr = $node->attributes;
        $classNode = $attr->getNamedItem('class');
        if ($classNode) {
            $classNode->nodeValue = \String::parseSimpleTokens(
                $classNode->nodeValue,
                $this->createTokensFromClassString($classNode->nodeValue)
            );
            $attr->setNamedItem($classNode);
        }
    }

    private function createTokensFromClassString($classString)
    {
        $tokens = array(
            'all'   => $classString
        );

        $chunks = preg_split('/ +/', $classString, -1, PREG_SPLIT_NO_EMPTY);
        $i = 0;

        foreach ($chunks as $class) {
            $tokens['class_' . $i] = $class;
            $i++;
        }

        return $tokens;
    }
} 
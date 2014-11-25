<?php

namespace Toflar\Contao\CssClassReplacer;


class XmlExporter
{
    /**
     * @var \DOMDocument
     */
    private $doc;

    /**
     * Exports all rules as XML
     *
     * @param \DataContainer $dc
     */
    public function export(\DataContainer $dc)
    {
        if (($ruleCollection = Rule::findBy('pid', $dc->id)) === null) {
            return;
        }

        $this->doc = new \DOMDocument('1.1', 'UTF-8');
        $rules = $this->doc->createElement('rules');

        foreach ($ruleCollection as $rule) {
            $rules->appendChild($this->createRuleElement($rule));
        }

        $this->doc->appendChild($rules);

        $file = new \File('system/tmp/' . md5(uniqid(mt_rand(), true)), true);
        $file->write($this->doc->saveXML());
        $file->close();
        $file->sendToBrowser('css_class_replacer_ruleset.xml');
        $file->delete();
    }

    /**
     * Creates a rule element
     *
     * @param Rule $model
     * @return \DOMElement
     */
    private function createRuleElement($model)
    {
        $rule = $this->doc->createElement('rule');
        $rule->setAttribute('type', $model->type);

        $selector = $this->doc->createElement('selector');
        $selectorValue = $this->doc->createCDATASection($model->selector);
        $selector->appendChild($selectorValue);

        $replacement = $this->doc->createElement('replacement');
        $replacementValue = $this->doc->createCDATASection($model->replacement);
        $replacement->appendChild($replacementValue);

        $published = $this->doc->createElement('published');
        $published->nodeValue = $model->published ? 'true' : 'false';

        $rule->appendChild($selector);
        $rule->appendChild($replacement);
        $rule->appendChild($published);

        return $rule;
    }
} 
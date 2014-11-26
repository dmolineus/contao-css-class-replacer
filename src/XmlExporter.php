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
        if (($ruleCollection = Rule::findBy('pid', $dc->id, array('order' => 'sorting'))) === null) {
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

        $type = $this->doc->createElement('type');
        $typeValue = $this->doc->createCDATASection($model->type);
        $type->appendChild($typeValue);

        $selector = $this->doc->createElement('selector');
        $selectorValue = $this->doc->createCDATASection($model->selector);
        $selector->appendChild($selectorValue);

        $enable_replace = $this->doc->createElement('enable_replace');
        $enable_replace->nodeValue = $model->enable_replace ? 'true' : 'false';

        $replace_directives = $this->doc->createElement('replace_directives');
        $replace_directivesValue = $this->doc->createCDATASection($model->replace_directives);
        $replace_directives->appendChild($replace_directivesValue);

        $enable_add = $this->doc->createElement('enable_add');
        $enable_add->nodeValue = $model->enable_add ? 'true' : 'false';

        $add_directives = $this->doc->createElement('add_directives');
        $add_directivesValue = $this->doc->createCDATASection($model->add_directives);
        $add_directives->appendChild($add_directivesValue);

        $published = $this->doc->createElement('published');
        $published->nodeValue = $model->published ? 'true' : 'false';

        $rule->appendChild($type);
        $rule->appendChild($selector);
        $rule->appendChild($enable_replace);
        $rule->appendChild($replace_directives);
        $rule->appendChild($enable_add);
        $rule->appendChild($add_directives);
        $rule->appendChild($published);

        return $rule;
    }
} 

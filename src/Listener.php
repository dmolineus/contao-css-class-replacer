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
     * @param $templateName
     * @return string
     */
    public function replaceCssClasses($buffer, $templateName)
    {
        $rules = $this->loadRules();
        if (empty($rules)) {
            return $buffer;
        }

        // No need to check for null values again as already done by Rule::findPublishedByActiveTheme()
        global $objPage;
        $layout = \LayoutModel::findByPk($objPage->layout);

        // Do not modify anything if the template name is not the one of the current page layout
        // e.g. when a developer calls Template::output() manually
        if ($layout->template !== $templateName) {
            return $buffer;
        }

        $stopWatch = new Stopwatch();
        $stopWatch->start('css_class_replacer');

        $this->doc = new \DOMDocument('1.1', $GLOBALS['TL_CONFIG']['characterSet']);
        $this->doc->strictErrorChecking = false;
        $this->loadHtml($buffer);

        $xPath = new \DOMXPath($this->doc);

        foreach ($rules as $rule) {
            try {
                $nodeList = $xPath->query($rule->getXPathExpr());

                foreach ($nodeList as $node) {
                    $rule->apply($node);
                }

            } catch (\Exception $e) {}
        }

        $this->addTimeToDebugBar($stopWatch);

        return $this->doc->saveHTML();
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

    /**
     * Get Rules.
     *
     * @return RuleInterface[]
     */
    private function loadRules()
    {
        $rules = array();

        if (($collection = RuleModel::findPublishedByActiveTheme()) !== null) {
            /** @var RuleModel $model */
            foreach ($collection as $model) {
                $rules[] = $model->getRule();
            }
        }

        return $rules;
    }
}

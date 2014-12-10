<?php

namespace Toflar\Contao\CssClassReplacer;


use Symfony\Component\Stopwatch\Stopwatch;

class Listener extends \Controller
{
    /**
     * Replace CSS classes
     *
     * @param $buffer
     * @param $templateName
     *
     * @throws \Exception If debug mode is enabled and something went wrong.
     *
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

        $manipulator = new DomManipulator($GLOBALS['TL_CONFIG']['characterSet'], $rules);
        $manipulator->loadHtml($buffer);

        try {
            $buffer = $manipulator->manipulate();
        } catch (\Exception $e) {
            if ($GLOBALS['TL_CONFIG']['debugMode']) {
                throw $e;
            }
        }

        $this->addTimeToDebugBar($stopWatch);

        return $buffer;
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

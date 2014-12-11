<?php

namespace Toflar\Contao\CssClassReplacer;

use Netzmacht\Contao\DomManipulator\Event\GetRulesEvent;
use Netzmacht\Contao\DomManipulator\Events;
use Netzmacht\DomManipulator\Filter\ValueFilter\TrimWhitespacesFilter;
use Netzmacht\DomManipulator\Query\XPathQuery;
use Netzmacht\DomManipulator\Rule\AttributeRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddRulesSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            Events::GET_RULES => array(
                array('addRules', 100),
            )
        );
    }

    public function addRules(GetRulesEvent $event)
    {
        if (($rules = RuleModel::findPublishedByActiveTheme()) === null) {
            return;
        }

        // No need to check for null values again as already done by Rule::findPublishedByActiveTheme()
        global $objPage;
        $layout = \LayoutModel::findByPk($objPage->layout);

        // Do not modify anything if the template name is not the one of the current page layout
        // e.g. when a developer calls Template::output() manually
        if ($layout->template !== $event->getTemplateName()) {
            return;
        }

        /**
         * @var $ruleModel RuleModel
         */
        foreach ($rules as $ruleModel) {

            $query = new XPathQuery($ruleModel->getXPathExpr());
            $rule = new AttributeRule($query, 'class');
            $rule->addFilter(new CssClassFilter($ruleModel->getDirectives()));
            $rule->addFilter(new TrimWhitespacesFilter());

            $event->addRule($rule);
        }
    }
} 
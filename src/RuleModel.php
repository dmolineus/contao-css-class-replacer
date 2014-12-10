<?php

namespace Toflar\Contao\CssClassReplacer;

use Symfony\Component\CssSelector\CssSelector;
use Toflar\Contao\CssClassReplacer\Rule\ClassAttributeRule;

class RuleModel extends \Model
{
    /**
     * Table name
     * @var string
     */
    static $strTable = 'tl_css_class_replacer';

    /**
     * Cached rule.
     * 
     * @var RuleInterface
     */
    private $rule;

    /**
     * Get the rule.
     *
     * @param bool $forceRefresh Rule is cached. Force to refresh cache if some modal values has changed.
     *
     * @return RuleInterface
     */
    public function getRule($forceRefresh = false)
    {
        if (!$this->rule || $forceRefresh) {
            $this->rule = $this->createRule();
        }
        
        return $this->rule;
    }

    /**
     * Get XPath expression
     *
     * @return string
     * @throws \Exception
     */
    public function getXPathExpr()
    {
        return $this->xpath_expression;
    }

    /**
     * Create and update XPath expression
     */
    public function updateXPathExpression()
    {
        if ($this->type === 'css') {
            $this->xpath_expression = CssSelector::toXPath($this->selector);
        } else {
            $this->xpath_expression = $this->selector;
        }
    }

    /**
     * Create and update the directives
     */
    public function updateDirectives()
    {
        $directives = array(
            'replace_search'        => array(),
            'replace_values'        => array(),
            'rgxp_replace_search'   => array(),
            'rgxp_replace_values'   => array(),
            'add'                   => array()
        );

        if ($this->enable_replace) {
            $replace = deserialize($this->replace_directives, true);

            // class => value (for str_replace())
            foreach ($replace as $row) {
                $key = $row['key'];

                // Regular expression
                if (substr($key, 0, 2) === 'r:') {
                    $key = substr($key, 2);
                    $directives['rgxp_replace_search'][]    = $key;
                    $directives['rgxp_replace_values'][]    = $row['value'];
                } else {
                    $directives['replace_search'][]         = $key;
                    $directives['replace_values'][]         = $row['value'];
                }
            }
        }

        if ($this->enable_add) {
            $directives['add'] = deserialize($this->add_directives, true);
        }

        $this->directives = json_encode($directives);
    }

    /**
     * Find by theme id
     *
     * @param int
     * @param array
     * @return static
     */
    public static function findPublishedByThemeId($id, array $arrOptions=array())
    {
        $t = static::$strTable;
        $arrColumns = array("$t.pid=?");

        if (!BE_USER_LOGGED_IN) {
            $arrColumns[] = "$t.published=1";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = 'sorting';
        }

        return static::findBy($arrColumns, $id, $arrOptions);
    }

    /**
     * Find by active theme
     *
     * @param array
     * @return static
     */
    public static function findPublishedByActiveTheme(array $arrOptions=array())
    {
        global $objPage;

        if ($objPage === null) {
            return null;
        }

        if (($layout = \LayoutModel::findByPk($objPage->layout)) === null) {
            return null;
        }

        return static::findPublishedByThemeId($layout->pid, $arrOptions);
    }

    /**
     * Create rule based on RuleModel.
     *
     * @return ClassAttributeRule
     */
    private function createRule()
    {
        // Set local cache copy for PHP 5.3 compatibility
        $cache = json_decode($this->directives, true);
        $rule  = new ClassAttributeRule($this->xpath_expression);

        // Apply simple replacement directives
        if (!empty($cache['replace_search'])) {
            $rule->addFilter(
                function ($class) use ($cache) {
                    return str_replace(
                        $cache['replace_search'],
                        $cache['replace_values'],
                        $class
                    );
                }
            );
        }

        // Apply rgxp replacement directives
        if (!empty($cache['rgxp_replace_search'])) {
            $rule->addFilter(
                function ($class) use ($cache) {
                    return preg_replace(
                        $cache['rgxp_replace_search'],
                        $cache['rgxp_replace_values'],
                        $class
                    );
                }
            );
        }

        // Apply rgxp replacement directives
        if (!empty($cache['add'])) {
            $rule->addFilter(
                function ($class) use ($cache) {
                    $class .= ' ' . implode(' ', $cache['add']);

                    return $class;
                }
            );
        }

        return $rule;
    }
}

<?php

namespace Toflar\Contao\CssClassReplacer;

use Symfony\Component\CssSelector\CssSelector;

class Rule extends \Model
{
    /**
     * Table name
     * @var string
     */
    static $strTable = 'tl_css_class_replacer';

    /**
     * Directives cache
     * @var array|null
     */
    private $directivesCache;

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
     * Apply replacement rules on given CSS class
     *
     * @param string
     */
    public function applyRulesOnClass($class)
    {
        if ($this->directivesCache === null) {
            $this->directivesCache = json_decode($this->directives, true);
        }

        // Apply simple replacement directives
        if ($this->directivesCache['replace_search']) {
            $class = str_replace($this->directivesCache['replace_search'],
                $this->directivesCache['replace_values'],
                $class);
        }

        // Apply rgxp replacement directives
        if ($this->directivesCache['rgxp_replace_search']) {
            $class = preg_replace($this->directivesCache['rgxp_replace_search'],
                $this->directivesCache['rgxp_replace_values'],
                $class);
        }

        // Apply add directives
        if ($this->directivesCache['add']) {
            $class .= ' ' . implode(' ', $this->directivesCache['add']);
        }

        // Clean output
        return trim(preg_replace('/\s+/',  ' ', $class));
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

        if (($layout = \LayoutModel::findByPk($objPage->layout)) === null) {
            return null;
        }

        return static::findPublishedByThemeId($layout->pid, $arrOptions);
    }
}

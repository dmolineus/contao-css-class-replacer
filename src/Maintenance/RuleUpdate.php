<?php

namespace Toflar\Contao\CssClassReplacer\Maintenance;

use Toflar\Contao\CssClassReplacer\BackendHelper;
use Toflar\Contao\CssClassReplacer\RuleModel;

class RuleUpdate implements \executable
{
    /**
     * Return true if the module is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return (\Input::post('FORM_SUBMIT') === 'css-class-replacer-rule-update');
    }

    /**
     * Generate the module
     *
     * @return string
     */
    public function run()
    {
        $template = new \BackendTemplate('be_css_class_replacer_update');
        $template->isActive = $this->isActive();
        $template->action = ampersand(\Environment::get('request'));
        $template->message = \Message::generate();
        $template->headline = specialchars($GLOBALS['TL_LANG']['tl_maintenance']['css-class-replacer-headline']);
        $template->description = specialchars($GLOBALS['TL_LANG']['tl_maintenance']['css-class-replacer-description']);
        $template->submit = specialchars($GLOBALS['TL_LANG']['tl_maintenance']['css-class-replacer-submit']);

        if ($this->isActive()) {
            $rules = RuleModel::findAll();
            $helper = new BackendHelper();
            $helper->updateCacheableValues($rules);

            \Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_maintenance']['css-class-replacer-message'],
                $rules->count())
            );

            \Controller::reload();
        }

        return $template->parse();
    }
} 

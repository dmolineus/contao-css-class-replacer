<?php

namespace Toflar\Contao\CssClassReplacer;


class XmlImporter
{
    /**
     * @var \DOMDocument
     */
    private $doc;

    /**
     * Imports rules from XML
     *
     * @param \DataContainer $dc
     * @return string
     */
    public function import(\DataContainer $dc)
    {
        $class = \BackendUser::getInstance()->uploader;

        if (!class_exists($class) || $class == 'DropZone') {
            $class = 'FileUpload';
        }

        $uploader = new $class();

        if (\Input::post('FORM_SUBMIT') == 'css_class_replacer_import') {
            $uploaded = $uploader->uploadTo('system/tmp');

            foreach ($uploaded as $file) {
                $this->importRuleSet($file, $dc->id);
            }

            // Let's be nice and update the cache automatically
            $rules = RuleModel::findAll();
            $helper = new BackendHelper();
            $helper->updateCacheableValues($rules);
        }

        // Return the form
        return '
<div id="tl_buttons">
<a href="' . ampersand(str_replace('&key=importCssClassReplacerRules', '', \Environment::get('request'))) . '" class="header_back" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']) . '" accesskey="b">' . $GLOBALS['TL_LANG']['MSC']['backBT'] . '</a>
</div>
' . \Message::generate() . '
<form action="' . ampersand(\Environment::get('request'), true) . '" id="css_class_replacer_import" class="tl_form" method="post" enctype="multipart/form-data">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="css_class_replacer_import">
<input type="hidden" name="REQUEST_TOKEN" value="' . REQUEST_TOKEN . '">

<div class="tl_tbox">
  <h3>' . $GLOBALS['TL_LANG']['tl_css_class_replacer']['source'][0] . '</h3>' . $uploader->generateMarkup() . (isset($GLOBALS['TL_LANG']['tl_css_class_replacer']['source'][1]) ? '
  <p class="tl_help tl_tip">' . $GLOBALS['TL_LANG']['tl_css_class_replacer']['source'][1] . '</p>' : '') . '
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="' . specialchars($GLOBALS['TL_LANG']['tl_css_class_replacer']['import'][0]) . '">
</div>

</div>
</form>';
    }


    /**
     * Import a rule set
     *
     * @param string
     * @param int
     */
    private function importRuleSet($file, $themeId)
    {
        $file = new \File($file, true);
        $doc = new \DOMDocument('1.1', 'UTF-8');
        $doc->loadXML($file->getContent());

        $rules     = $doc->getElementsByTagName('rule');
        $sortIndex = 0;
        $lastRule  = RuleModel::findBy('pid', $themeId, array('order' => 'sorting DESC', 'limit' => 1));

        if ($lastRule) {
            $sortIndex = $lastRule->sorting + 128;
        }

        foreach ($rules as $rule) {
            $set = array(
                'pid'                   => $themeId,
                'sorting'               => $sortIndex,
                'type'                  => $rule->getElementsByTagName('type')->item(0)->nodeValue,
                'selector'              => $rule->getElementsByTagName('selector')->item(0)->nodeValue,
                'enable_replace'        => (($rule->getElementsByTagName('enable_replace')->item(0)->nodeValue === 'true') ? '1' : ''),
                'replace_directives'    => $rule->getElementsByTagName('replace_directives')->item(0)->nodeValue,
                'enable_add'            => (($rule->getElementsByTagName('enable_add')->item(0)->nodeValue === 'true') ? '1' : ''),
                'add_directives'        => $rule->getElementsByTagName('add_directives')->item(0)->nodeValue,
                'published'             => (($rule->getElementsByTagName('published')->item(0)->nodeValue === 'true') ? '1' : ''),
            );

            $sortIndex += 128;

            \Database::getInstance()->prepare('INSERT INTO tl_css_class_replacer %s')
                ->set($set)
                ->execute();
        }
    }
}

<?php
namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class LanguageController extends BaseController {
    protected $add_flag = false;
    public $languages = [];

    public function __construct() {
        parent::__construct();

        if ($this->trp_available) {
            parent::__construct();

            add_shortcode('propel-language-switcher', [$this, 'custom_language_switcher'], 10);
        }
    }

    public function custom_language_switcher() {
        global $propel;

        ob_start();

        $langs = trp_custom_language_switcher();

        foreach ($langs as $name => $item) {    
            $item_lang = $item['short_language_name'];
            if (strpos($item_lang, '_'))
                $item_lang = explode('_', $item_lang)[1];

            if (strtolower(PROPELLER_LANG) != $item_lang && isset($propel['url_slugs'])) {
                $link_chunks = explode('/', $item['current_page_url']);

                $slug = $this->get_lang_slug($item['short_language_name'], $propel['url_slugs']);

                if ($slug != "") {
                    $link_chunks[count($link_chunks) - 2] = $slug;

                    $item['current_page_url'] = implode('/', $link_chunks);
                }
            }

            $this->languages[$name] = $item;
        }

        require $this->load_template('partials', '/other/propeller-language-switcher.php');
        
        return ob_get_clean();
    }

    private function get_lang_slug($lang, $slugs) {
        $found = array_filter($slugs, function($obj) use($lang){
            return strtolower($obj->language) == strtolower($lang);
        });

        if (count($found))
            return current($found)->value;

        return "";
    }
}

?>
<?php

namespace Propeller\Admin;

if ( ! defined( 'ABSPATH' ) ) exit;

use Gettext\Translation;
use Propeller\Includes\Controller\ValuesetController;
use stdClass;

class PropellerValuesets extends PropellerTranslations {
    const VALUESETS_CONTEXT = 'valuesets';

    public function __construct() {
        parent::__construct();
    }

    public function sync_valuesets() {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        
        $valuesetsController = new ValuesetController();
        
        $return = new stdClass();
        $return->success = true;
        
        $pages = 1; 
        $offset = 20;

        for ($page = 1; $page <= $pages; $page++) {
            $valuesets = $valuesetsController->get_valuesets(['page' => $page, 'offset' => $offset]);

            if (is_object($valuesets) && isset($valuesets->itemsFound) && $valuesets->itemsFound > 0) {
                if ($pages == 1 && $page == 1) {
                    $return->message = __("Synchronized valuesets: ", "propeller-ecommerce-v2") . $valuesets->itemsFound; 
                    $pages = $valuesets->pages;
                }                    

                $translatables = [];
                $languages = [];
                $default_lang = null;

                foreach ($valuesets->items as $valset) {
                    if (is_array($valset->descriptions) && count($valset->descriptions)) {
                        foreach ($valset->descriptions as $desc) {
                            $translatables[] = [
                                $desc->language => $desc->value
                            ];

                            if (!in_array($desc->language, $languages))
                                $languages[] = $desc->language;
                        }
                    }

                    if ($valset->valuesetItems->itemsFound > 0) {
                        foreach ($valset->valuesetItems->items as $item) {
                            if (is_array($item->descriptions) && count($item->descriptions)) {
                                foreach ($item->descriptions as $desc) {
                                    if (!is_numeric($desc->value)) {
                                        $translatables[$item->value] = [
                                            $desc->language => $desc->value
                                        ];
                                    }

                                    if (!in_array($desc->language, $languages))
                                        $languages[] = $desc->language;
                                }
                            }
                        }
                    }
                }

                if (sizeof($translatables)) {
                    $domain = PROPELLER_PLUGIN_NAME;
                    $template_file = $this->translations_path . DIRECTORY_SEPARATOR . $domain . '.pot';
                    $existing_translations = $this->load_translation($template_file);

                    $new_valset_trans_arr = [];
                    $prev_valset_trans_arr = [];
                    
                    // get all translations with the 'valuesets' context
                    foreach ($existing_translations->getTranslations() as $tr) {
                        if ($tr->getContext() == self::VALUESETS_CONTEXT)
                            $prev_valset_trans_arr[] = $tr;
                    }

                    foreach ($translatables as $value => $translatable) {
                        $inner_languages = array_keys($translatable);
                        
                        if (!$default_lang) {
                            if (in_array("EN", $inner_languages))
                                $default_lang = "EN";
                            else if (in_array("NL", $inner_languages))
                                $default_lang = "NL";
                        }
                        
                        $value = $translatable[$default_lang];
                        $value = htmlspecialchars_decode($value);
                        $value = stripslashes($value);

                        $translation = Translation::create(self::VALUESETS_CONTEXT, $value);

                        $new_valset_trans_arr[] = $translation;

                        if (!$existing_translations->has($translation))                          
                            $existing_translations->add($translation);
                    }

                    // Delete all possible translations that are not in the new translations array
                    $deleted_translations = [];
                    foreach ($prev_valset_trans_arr as $prev_trans) {
                        $trans_found = array_filter($new_valset_trans_arr, function($tr) use ($prev_trans) {
                            return $tr->getOriginal() == $prev_trans->getOriginal();
                        });

                        if (!count($trans_found)) {
                            $deleted_translations[] = $prev_trans;

                            $existing_translations->remove($prev_trans);
                        }
                    }
                    
                    // Save template file with definitions for valuesets
                    $this->apply_headers($existing_translations);
                    $this->po_generator->generateFile($existing_translations, $template_file);

                    // Merge the newest valueset strings into the translations files and add translations
                    $this->get_files();

                    $lang_translations = [];

                    foreach ($this->translations as $translation_file) {
                        $translations_language = $this->get_file_language($translation_file);

                        $lang_translations[$translations_language] = $this->load_translation($translation_file);

                        $merged_translations = $lang_translations[$translations_language]->mergeWith($existing_translations);

                        foreach ($deleted_translations as $del) 
                            $merged_translations->remove($del);

                        foreach ($translatables as $index => $translatable) { 
                            if ($translations_language == $default_lang && $default_lang == 'EN')
                                continue;

                            // $original = $translatable[$default_lang];
                            $translation_string = $translatable[$translations_language];

                            if ($translation_string) {
                                $valset_translation = $merged_translations->find(self::VALUESETS_CONTEXT, $translation_string);

                                if ($valset_translation) {
                                    $translation_string = htmlspecialchars_decode($translation_string);
                                    $translation_string = stripslashes($translation_string);
                
                                    $valset_translation->translate($translation_string);
                                } 
                            }                            
                        }

                        $this->po_generator->generateFile($merged_translations, $translation_file);

                        $path_parts = pathinfo($translation_file);

                        $this->mo_generator->generateFile($merged_translations, $this->translations_path . DIRECTORY_SEPARATOR . sanitize_text_field( $path_parts['filename'] ) . '.mo' );
                    }
                }
            }
        }

        die(json_encode($return));
    }

    public function get_file_language($file_name) {
        $name_chunks = explode('-', $file_name);

        $locale = explode('.', $name_chunks[sizeof($name_chunks) - 1])[0];

        if (str_contains($locale, '_'))
            $locale_lang = explode('_', $locale)[1];
        else 
            $locale_lang = $locale;

        if ($locale_lang == 'US' || $locale_lang == 'GB')
            return "EN";

        return strtoupper($locale_lang);
    }
}
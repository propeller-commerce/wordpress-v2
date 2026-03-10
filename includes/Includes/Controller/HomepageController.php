<?php

namespace Propeller\Includes\Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

class HomepageController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function home_page()
    {
        ob_start();

        require $this->load_template('templates', '/propeller-home-page.php');

        return ob_get_clean();
    }

    public function breadcrumbs($paths)
    {
        global $propel;

        if (isset($propel['breadcrumbs'])) {
            //$propel['breadcrumbs'][] = $paths[0]; // add data for the item being viewed, like product for example
            $paths = $propel['breadcrumbs'];
        }

        require $this->load_template('partials', '/other/propeller-breadcrumbs.php');
    }
}

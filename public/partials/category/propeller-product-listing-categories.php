<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

if (!function_exists('buildCatalogListingMenu')) {
    function buildCatalogListingMenu($categories, $classes = [], $index = 0, $ul_aria_id = '', $search_categories = [], $data)
    {
        $menuObj = new Propeller\Includes\Controller\MenuController();
        $slug = get_query_var('slug');

        $object_id = null;
        if (PROPELLER_ID_IN_URL)
            $object_id = get_query_var('obid');

        $ul_class = isset($classes[$index]) && isset($classes[$index]['ul_classes']) ? $classes[$index]['ul_classes'] : '';
        $li_class = isset($classes[$index]) && isset($classes[$index]['li_classes']) ? $classes[$index]['li_classes'] : '';
        $ul_id = 'submenu_' . $ul_aria_id . '';
        $str = '<ul class="' . $ul_class . '" id="' . $ul_id . '">';

        foreach ($categories as $cat) {
            if (!count($cat->slug) || empty($cat->slug[0]->value))
                continue;

            $subMenuIndex = $cat->categoryId;

            $str .= '<li class="' . $li_class . '">';
            $spanClass = (isset($cat->categories) && sizeof($cat->categories)) ? 'has-submenu dropdown-toggle' : '';
            $aClass = (isset($cat->categories) && sizeof($cat->categories)) ? 'has-children' : '';
            $aLabel = (isset($cat->categories) && sizeof($cat->categories)) ? 'data-bs-toggle="collapse" href="#submenu_' . $subMenuIndex . '" aria-expanded="false" data-bs-target="#submenu_' . $subMenuIndex . '"' : '';

            $menu_url = $menuObj->buildUrl(PageController::get_slug(PageType::CATEGORY_PAGE), $cat->slug[0]->value, $cat->urlId);

            if (sizeof($search_categories) && isset($search_categories[$cat->categoryId])) {
                $query_term = wp_unslash(get_query_var('term'));

                if (!empty($query_term))
                    $menu_url .= '?term=' . $query_term;

                // if (!empty(get_query_var('plate')))
                //     $menu_url .= '?plate=' . get_query_var('plate');
            }
            //var_dump(!empty($slug) && $slug == $cat->slug[0]->value);

            $obid = '';
            if (PROPELLER_ID_IN_URL) {
                $obid = ' data-obid="' . $cat->urlId . '"';

                if ((int) $object_id == (int) $cat->urlId)
                    $aClass .= ' active';
            } else {
                if (isset($data) && isset($data->urlId) && $cat->urlId == $data->urlId)
                    $aClass .= ' active';
                else {
                    // if (!empty($slug) && $slug == $cat->slug[0]->value)
                    //     $aClass .= ' active';
                }
            }

            $str .= '<a href="' . $menu_url . '" class=" ' . $aClass . '" >';
            $str .= $cat->name[0]->value;

            if (sizeof($search_categories)) {
                if (isset($search_categories[$cat->categoryId]))
                    $str .= " (" . $search_categories[$cat->categoryId]->items . ")";
            }

            $str .= (isset($cat->categories) && sizeof($cat->categories)) ? '</a><span class=" arrow-down ' . $spanClass . '" ' . $aLabel . ' ' . $obid . '></span>' : '</a>';

            if (isset($cat->categories) && sizeof($cat->categories)) {
                $subindex = $index + 1;
                $str .= buildCatalogListingMenu($cat->categories, $classes, $subindex, $subMenuIndex, $search_categories, $data);
            }

            $str .= '</li>';
        }

        $str .= '</ul>';

        return $str;
    }
}

$menucontroller = new Propeller\Includes\Controller\MenuController();
$menuItems = $menucontroller->getMenu();
?>

<nav class="catalog-menu d-none d-md-block">
    <div class="filter categories-nav">
        <button class="btn-filter" type="button" href="#filterForm_catalog_menu" data-bs-toggle="collapse" aria-expanded="false" aria-controls="filterForm_catalog_menu">
            <?php echo esc_html(__('Categories', 'propeller-ecommerce-v2')); ?>
        </button>
        <div class="catalog-filter-content collapse show" id="filterForm_catalog_menu">
            <ul>
                <?php

                $classes = [
                    0 => [  // first level UL classes
                        'ul_classes' => 'main-propeller-category',
                        'li_classes' => 'main-item'
                    ],
                    1 => [ // second level UL classes
                        'ul_classes' => 'main-propeller-category-submenu collapse',
                        'li_classes' => 'main-subitem'
                    ],
                    2 => [ // third level UL classes
                        'ul_classes' => 'main-propeller-category-subsubmenu collapse',
                        'li_classes' => 'main-subsubitem'
                    ],
                ];
                //var_dump($menucontroller);
                echo wp_kses_post(buildCatalogListingMenu($menuItems->categories, $classes, 0, '', [], $data));

                ?>
            </ul>
        </div>
    </div>
</nav>
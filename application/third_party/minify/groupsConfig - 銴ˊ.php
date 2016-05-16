<?php

/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 * http://code.google.com/p/minify/wiki/CustomSource#Non-File_Sources
 * plz see this page for remote js fetching
 * */
define('MIN_WWW_PUBLIC_LIB', '//');
define('MIN_SEARCH_PUBLIC_LIB', '//');
define('MIN_WWW_LIB', '//lib/' . COUNTRY_REGION . '_' . LANGUAGE . '/');
define('MIN_SEARCH_LIB', '//lib/' . COUNTRY_REGION . '_' . LANGUAGE . '/');

$g_str = explode('_', $_GET['g']);
$type = end($g_str);
$common = array();

// return js 
if ($type == 'js') {
    // global setup 
    $common = array(
        'index' => array(
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-ui-1.10.3.custom.min.js',
            MIN_WWW_PUBLIC_LIB . 'js/autocomplete.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery.fancybox.pack.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery.slides.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery.lazyload.js',
            MIN_WWW_PUBLIC_LIB . 'js/jQuery-menu-aim-master/jquery.menu-aim.js',
            MIN_WWW_LIB . 'js/website.js',
            MIN_WWW_LIB . 'js/index_content.js',
            MIN_WWW_LIB . 'js/ga.js'
        ),
        'market' => array(
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-ui-1.10.3.custom.min.js',
            MIN_WWW_PUBLIC_LIB . 'js/autocomplete.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery.fancybox.pack.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery.slides.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery.lazyload.js',
            MIN_WWW_PUBLIC_LIB . 'js/jQuery-menu-aim-master/jquery.menu-aim.js',
            MIN_WWW_LIB . 'js/website.js',
            MIN_WWW_LIB . 'js/market_content.js',
            MIN_WWW_LIB . 'js/ga.js'),
        'category' => array(
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-ui-1.10.3.custom.min.js',
            MIN_WWW_PUBLIC_LIB . 'js/autocomplete.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery.simplePagination.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery.tmpl.min.js',
            MIN_WWW_PUBLIC_LIB . '/js/jquery/jquery.lazyload.js',
            MIN_WWW_LIB . 'js/website.js',
            MIN_WWW_PUBLIC_LIB . '/js/site_page/site_category_cat.js',
            MIN_WWW_PUBLIC_LIB . '/js/site_menu.js',
            MIN_WWW_PUBLIC_LIB . 'js/jQuery-menu-aim-master/jquery.menu-aim.js',
            MIN_WWW_LIB . 'js/shopcart.js',
            MIN_WWW_PUBLIC_LIB . 'lib/js/call_spec.js',
            MIN_WWW_LIB . 'js/category.js',
            MIN_WWW_LIB . 'js/category_left.js',
            MIN_WWW_LIB . 'js/ga.js'),
        'item' => array(
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-ui-1.10.3.custom.min.js',
            MIN_WWW_PUBLIC_LIB . 'js/autocomplete.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery.base64.js',
            MIN_WWW_PUBLIC_LIB . 'js/jQuery-menu-aim-master/jquery.menu-aim.js',
            MIN_WWW_LIB . 'js/website.js',
            MIN_WWW_LIB . 'js/shopcart.js',
            MIN_WWW_LIB . 'js/ga.js',
            MIN_WWW_LIB . 'js/itempage.js',
            MIN_WWW_LIB . 'js/item.js',
            MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery.lwtCountdown-1.0.js'
        ),
        'search' => array(
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery/jquery-ui-1.10.3.custom.min.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/autocomplete.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery/jquery.simplePagination.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery/jquery.tmpl.min.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery/jquery.tmpl.html.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery/jquery.lazyload.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/jQuery-menu-aim-master/jquery.menu-aim.js',
            MIN_SEARCH_LIB . 'js/header_menu.js',
            MIN_SEARCH_LIB . 'js/website.js',
            MIN_SEARCH_LIB . 'js/shopcart.js',
            MIN_SEARCH_PUBLIC_LIB . 'lib/js/search.js',
            MIN_SEARCH_PUBLIC_LIB . 'lib/js/call_spec.js',
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery-loadmask/jquery.loadmask.min.js',
        )
    );

    // Area setup 
    // 區域設定js 將優先讀取
    if ($g_str[1] != $type) {
        switch ($g_str[1]) {
            case 'tw':
                array_unshift(
                    $common[$g_str[0]], 
                  //  MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-1.9.1.min.js',
                    MIN_WWW_PUBLIC_LIB . 'js/jquery.cookie.js', // feuniu 啟用cookie 
                    MIN_WWW_PUBLIC_LIB . 'js/jquery.fancybox.pack.js'
                );
                break;
            case 'sh':
                array_unshift(
                    $common[$g_str[0]], 
                   // MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-1.9.1.min.js',
                    MIN_WWW_PUBLIC_LIB . 'js/jquery.cookie.js', // feuniu 啟用cookie 
                    MIN_WWW_PUBLIC_LIB . 'js/jquery.fancybox.pack.js'
                );
                break;
            case 'sg':
                array_unshift(
                    $common['category'], 
                   // MIN_WWW_PUBLIC_LIB . 'js/jquery/jquery-1.9.1.min.js',
                    MIN_WWW_PUBLIC_LIB . '/js/desandro/masonry.pkgd.min.js'
                );
                break;
        }
    }
}
// return css
if ($type == 'css') {
    $common = array(
        'index' => array(
            MIN_WWW_LIB . 'css/jquery-ui-1.10.3.css',
            MIN_WWW_LIB . 'css/store.css',
            MIN_WWW_LIB . 'css/jquery.fancybox.css',
            MIN_WWW_PUBLIC_LIB . 'js/fancybox/source/jquery.fancybox.css'
        ),
        'market' => array(
            MIN_WWW_LIB . 'css/jquery-ui-1.10.3.css',
            MIN_WWW_LIB . 'css/store.css',
            MIN_WWW_LIB . 'css/jquery.fancybox.css',
            MIN_WWW_PUBLIC_LIB . 'js/fancybox/source/jquery.fancybox.css'
        ),
        'category' => array(
            MIN_WWW_LIB . 'css/jquery-ui-1.10.3.css',
            MIN_WWW_PUBLIC_LIB . 'css/simplePagination.css',
            MIN_WWW_PUBLIC_LIB . 'js/fancybox/source/jquery.fancybox.css'
        ),
        'item' => array(
            MIN_WWW_LIB . 'css/store.css',
            MIN_WWW_LIB . 'css/jquery-ui-1.10.3.css',
        ),
        'search' => array(
           // MIN_SEARCH_LIB . 'css/jquery-ui-1.10.3.css',
            MIN_SEARCH_PUBLIC_LIB . 'css/simplePagination.css',
            MIN_SEARCH_PUBLIC_LIB . 'js/jquery-loadmask/jquery.loadmask.css',
            MIN_SEARCH_PUBLIC_LIB . 'js/fancybox/source/jquery.fancybox.css'
        )
    );
}
$return[$_GET['g']] = $common[$g_str[0]];

if ($_GET['o'] == 'debug') {
    echo "<PRE>";
    print_r($return);
} else {
    return $return;
}
?>
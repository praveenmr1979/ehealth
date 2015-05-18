<?php

/**
 * @file
 * This file is empty by default because the base theme chain (Alpha & Omega) provides
 * all the basic functionality. However, in case you wish to customize the output that Drupal
 * generates through Alpha & Omega this file is a good place to do so.
 * 
 * Alpha comes with a neat solution for keeping this file as clean as possible while the code
 * for your subtheme grows. Please read the README.txt in the /preprocess and /process subfolders
 * for more information on this topic.
 */
 
 function ehealth_menu_item($link, $has_children, $menu = 'mainmenu', $in_active_trail = FALSE, $extra_class = NULL) {
exit;
  $class = ($menu ? 'expanded' : ($has_children ? 'collapsed' : 'leaf123'));

  if (!empty($extra_class))
    $class .= ' '. $extra_class;

  if ($in_active_trail)
    $class .= ' active-trail';

  $class .= ' ' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower(strip_tags($link)));

  return '<li class="'. $class .'">'. $link . $menu ."</li>\n";
}

function ehealth_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    // Adding the title of the current page to the breadcrumb.
    $breadcrumb[] = drupal_get_title();
    
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

    $output .= '<div class="breadcrumb">' . implode(' » ', $breadcrumb) . '</div>';
    return $output;
  }
}

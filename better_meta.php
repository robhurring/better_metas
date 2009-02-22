<?php
/*
Plugin Name: Better Meta
Plugin URI: http://github.com/robhurring/wordpress-better_meta
Description: Provides a nice syntax for your custom fields/meta data to use in themes/plugins
Version: 1.0
Author: Rob Hurring <rob@zerobased.com>
Author URI: http://zerobased.com
*/

/**
 * Provides a convenience method for +get_post_meta+ but will return a single field or array automatically
 * it supports WP style arguments as well, so a value of "a=b&c=d" will parse out into an assoc. array
 * 
 * Example:
 *  a custom field with key = "options" and value = "sort=asc&show_link=1" would be accessed via
 *    +meta('options');+   =>  +array('sort' => 'asc', 'show_link' => 1)+
 * 
 * @param string $name  name of the custom field to get
 * @return array    if there is more than 1 key available
 * @return string   if there is only 1 string available
 */
function meta($name = '')
{
	global $post;
	if($_ = get_post_meta($post->ID, $name, false))
    foreach($_ as $key => $value) 
      if(stristr($value, '='))
        $_[$key] = wp_parse_args($value);
	return (is_array($_) && count($_) == 1) ? array_pop($_) : $_;
}

// builds an array of _all_ meta data and their values
/**
 * Gets all custom fields within a certain page or post and parses them out into an array
 * with +meta()+
 * 
 * Example:
 *  a post with the following custom fields/values:
 *    key = options, value = 'sort=asc&show_link=1'
 *    key = subtitle, value = 'my subtitle'
 *    key = image, value = 'image01.jpg'
 *    key = image, value = 'image02.jpg'
 * 
 * when run with +metas()+ would produce:
 *  
 *    array(
 *      'options' => array('sort' => 'asc', 'show_link' => 1), 
 *      'images' => array('image01.jpg', 'image02.jpg'),
 *      'subtitle' => 'my subtitle')
 * 
 * @return array  on array of all custom fields *except those beginning with an underscore* (those are considered private)
 */
function metas()
{
	global $post;
	$_ = array();
	if($keys = get_post_custom_keys($post->ID))
	{
  	foreach($keys as $index => $key)
  	{
  		if(substr($key, 0, 1) == '_') continue;	// ignore _key's as those are wordpress-specific
  		$_[$key] = meta($key);
  	}
  }
	return $_;
}

/**
 * Simple wrapper to check if a post/page has a certain custom field
 * 
 * @param string $key   name of the custom field you want to check
 * @return bool
 */
function meta_key_exist($key = '')
{
  global $post;
  $_ = get_post_custom_keys($post->ID);
  return is_array($_) ? in_array($key, $_) : false;
}



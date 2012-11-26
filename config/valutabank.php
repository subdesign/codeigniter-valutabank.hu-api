<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Valutabank API config file
 */

$config['currencies'] = array('USD', 'EUR', 'CHF');   // array of currencies or the string "all" if you want all
$config['returntype'] = 'html'; 	// html OR array
$config['icon_path']  = 'assets/images/';	// relative path from base url
$config['icon_name']  = 'icon';		// icon name prefix. it will be "icon-usd", "icon-eur" etc.
$config['icon_ext']   = 'jpg';	  // extension of icon image files
<?php
/**
 * Pages drafts
 * 
 * @package PagesDrafts
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Register init
elgg_register_event_handler('init', 'system', 'pages_drafts_init');

// Init
function pages_drafts_init() {
	// register the pages JS
	$pages_js = elgg_get_simplecache_url('js', 'pagesdrafts/drafts');
	elgg_register_simplecache_view('js/pagesdrafts/drafts');
	elgg_register_js('elgg.pagesdrafts', $pages_js);

	// Extend pages page handler
	elgg_register_plugin_hook_handler('route', 'pages', 'pagesdrafts_route_groups_handler');
}


// Hook into group routing to provide extra content
function pagesdrafts_route_groups_handler($hook, $type, $return, $params) {
	elgg_load_js('elgg.pagesdrafts');
	return $return;
}
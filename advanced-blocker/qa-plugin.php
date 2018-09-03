<?php
/*
	Plugin Name: Advanced Blocker
	Plugin URI: https://www.github.com/jacksiro/Q2A-Advanced-Blocker-Plugin
	Plugin Description: Give normal site users the priviledge to block users
	Plugin Version: 1.1
	Plugin Date: 2018-08-02
	Plugin Author: Jackson Siro
	Plugin Author URI: https://www.github.com/jacksiro
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.6
	Plugin Update Check URI: https://www.github.com/jacksiro/Q2A-Advanced-Blocker-Plugin/master/advanced-blocker/qa-plugin.php

*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
	
}

	$plugin_dir = dirname( __FILE__ ) . '/';
	$plugin_url = qa_path_to_root().'qa-plugin/advanced-blocker';
	define( "QA_AUBLOCKER_DIR",  $plugin_url.'/'  );
	
	qa_register_plugin_phrases('ab-lang-*.php', 'ab_lang');
	qa_register_plugin_overrides('ab-overrides.php');
	qa_register_plugin_layer('ab-layer.php', 'Advanced Blocker Layer');
	qa_register_plugin_module('page', 'ab-blocked.php', 'blocked_users', 'Blocked Users Page');
	qa_register_plugin_module('user', 'ab-blocker.php', 'advanced_blocker', 'Advanced Blocker');
	
	function ab_get_blocked_users($blocker, $count)
	{
		$users = qa_db_read_all_assoc(qa_db_query_sub(
			"SELECT userid, UNIX_TIMESTAMP(blocked) AS blocked, reason, appeal, flags WHERE blocker=$ ORDER BY created DESC LIMIT #", 
			$blocker, $count
		));
		return $users;
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/

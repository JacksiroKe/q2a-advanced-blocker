<?php
/*
	Plugin Name: Advanced Blocker
	Plugin URI: https://github.com/JacksiroKe/q2a-advanced-blocker
	Plugin Description: Give normal site users the priviledge to block users
	Plugin Version: 1.2
	Plugin Date: 2018-08-02
	Plugin Author: JacksiroKe
	Plugin Author URI: https://github.com/JacksiroKe
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.6

*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
	
}
	
qa_register_plugin_module('widget', 'advanced-blocker.php', 'advanced_blocker', 'Advanced Blocker');

/*
	Omit PHP closing tag to help avoid accidental output
*/

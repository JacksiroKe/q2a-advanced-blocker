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
	Plugin Update Check URI: https://github.com/JacksiroKe/q2a-advanced-blocker/master/qa-plugin.php

*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
	
}
	
qa_register_plugin_module('widget', 'qa-advanced-blocker.php', 'qa_advanced_blocker', 'Advanced Blocker');

/*
	Omit PHP closing tag to help avoid accidental output
*/

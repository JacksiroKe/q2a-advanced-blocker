<?php
/*
	Advanced Blocker by Jackson Siro
	https://www.github.com/jacksiro/Q2A-Advanced-Blocker-Plugin

	Description: Plugin Language phrases

*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../../');
	exit;
}

	return array(
		'block_as_admin_button' => 'Block as Admin',
		'block_user_button_x' => 'Block ^',
		'block_user_button_you_x' => 'Block ^ as You',
		'blocked' => 'blocked',
		'blocked_users' => 'Users You\'ve Blocked',
		'view_blocked_users_1' => 'You\'ve Blocked 1 User',
		'view_blocked_users_x' => 'You\'ve Blocked ^ Users',
		'unblock_as_admin_button' => 'Unblock as Admin',
		'unblock_user_button_x' => 'Unblock ^',
		'unblock_user_button_you_x' => 'Unblock ^ as You',
	);

/*
	Omit PHP closing tag to help avoid accidental output
*/

<?php
/*
	Advanced Blocker by Jackson Siro
	https://github.com/JacksiroKe/q2a-advanced-blocker

	Description: Give normal site users the priviledge to block users

*/

class qa_advanced_blocker
{
	public function admin_form(&$qa_content)
	{
		return array(
			'fields' => array(
				array(
					'type' => 'custom',
					'label' => 'Hey <b>'.qa_get_logged_in_handle().'</b>, this is a <a href="https://github.com/JacksiroKe/q2a-advanced-blocker"><b>Premium Plugin</b></a>! To get the full experience, simply purchase it by sending <b>$30</b> to <b>jaksiro@gmail.com</b> via <a href="https://paypal.com"><b>Paypal</b></a> to get the upgrade link on email.',
				),
				
			),
		);
	}

}

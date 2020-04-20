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
					'label' => 'Hey <b>'.qa_get_logged_in_handle().'</b>, I am glad you intrested in my plugin: <a href="https://github.com/JacksiroKe/q2a-advanced-blocker"><b>Advanced Blocker</b></a>.<br> Well, this is a <b>Premium Plugin</b>! Purchase it by sending <b>$40</b> to <a href="https://paypal.me/jacksiro"><b>paypal.me/jacksiro</b></a> to get the upgrade link on email.<hr>If you have any queries email me asap on <a href="mailto:jaksiro@gmail.com">jaksiro@gmail.com</a> and I will get back to you asap!',
				),
				
			),
		);
	}

}

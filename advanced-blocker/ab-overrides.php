<?php

/*
	Advanced Tags
	https://www.github.com/jacksiro/Q2A-Advanced-Tags-Plugin
	
	Advance your tags with description, image, wiki, adverts and so much more
	
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
	/*
		$requestparts = qa_request_parts();
		if (is_numeric($requestparts[0])) {
			return 'ipblock';
		}
	*/	
	
	function qa_page_q_post_rules($post, $parentpost = null, $siblingposts = null, $childposts = null)
	{
		$userid = qa_get_logged_in_userid();
		$cookieid = qa_cookie_get();
		$userlevel = qa_user_level_for_post($post);

		$userfields = qa_get_logged_in_user_cache();
		if (!isset($userfields)) {
			$userfields = array(
				'userid' => null,
				'level' => null,
				'flags' => null,
			);
		}

		$blockedme = qa_db_read_all_values(qa_db_query_sub(
			'SELECT userid FROM ^userblocks WHERE blocker=$ AND userid=$',
			$post['userid'], $userid
		));
		
		$rules['isbyuser'] = qa_post_is_by_user($post, $userid, $cookieid);
		$rules['closed'] = $post['basetype'] == 'Q' && (isset($post['closedbyid']) || (isset($post['selchildid']) && qa_opt('do_close_on_select')));

		// Cache some responses to the user permission checks

		$permiterror_post_q = qa_user_permit_error('permit_post_q', null, $userlevel, true, $userfields); // don't check limits here, so we can show error message
		$permiterror_post_a = qa_user_permit_error('permit_post_a', null, $userlevel, true, $userfields);
		$permiterror_post_c = qa_user_permit_error('permit_post_c', null, $userlevel, true, $userfields);

		$edit_option = $post['basetype'] == 'Q' ? 'permit_edit_q' : ($post['basetype'] == 'A' ? 'permit_edit_a' : 'permit_edit_c');
		$permiterror_edit = qa_user_permit_error($edit_option, null, $userlevel, true, $userfields);
		$permiterror_retagcat = qa_user_permit_error('permit_retag_cat', null, $userlevel, true, $userfields);
		$permiterror_flag = qa_user_permit_error('permit_flag', null, $userlevel, true, $userfields);
		$permiterror_hide_show = qa_user_permit_error('permit_hide_show', null, $userlevel, true, $userfields);
		$permiterror_hide_show_self = $rules['isbyuser'] ? qa_user_permit_error(null, null, $userlevel, true, $userfields) : $permiterror_hide_show;

		$close_option = $rules['isbyuser'] && !$blockedme && qa_opt('allow_close_own_questions') ? null : 'permit_close_q';
		$permiterror_close_open = qa_user_permit_error($close_option, null, $userlevel, true, $userfields);
		$permiterror_moderate = qa_user_permit_error('permit_moderate', null, $userlevel, true, $userfields);

		// General permissions

		$rules['authorlast'] = !isset($post['lastuserid']) || $post['lastuserid'] === $post['userid'];
		$rules['viewable'] = $post['hidden'] ? !$permiterror_hide_show_self : ($post['queued'] ? ($rules['isbyuser'] || !$permiterror_moderate) : true);

		// Answer, comment and edit might show the button even if the user still needs to do something (e.g. log in)

		$rules['answerbutton'] = $post['type'] == 'Q' && !$blockedme && $permiterror_post_a != 'level' && !$rules['closed'] && (qa_opt('allow_self_answer') || !$rules['isbyuser']);

		$rules['commentbutton'] = ($post['type'] == 'Q' || $post['type'] == 'A') && !$blockedme && $permiterror_post_c != 'level' && qa_opt($post['type'] == 'Q' ? 'comment_on_qs' : 'comment_on_as');
		$rules['commentable'] = $rules['commentbutton'] && !$permiterror_post_c;

		$button_errors = array('login', 'level', 'approve');

		$rules['editbutton'] = !$post['hidden'] && !$rules['closed'] && !$blockedme && 
		($rules['isbyuser'] || (!in_array($permiterror_edit, $button_errors) && (!$post['queued'])));
		$rules['editable'] = $rules['editbutton'] && !$blockedme && ($rules['isbyuser'] || !$permiterror_edit);

		$rules['retagcatbutton'] = $post['basetype'] == 'Q' && !$blockedme && 
		(qa_using_tags() || qa_using_categories()) && !$post['hidden'] && 
		($rules['isbyuser'] || !in_array($permiterror_retagcat, $button_errors));
		$rules['retagcatable'] = $rules['retagcatbutton'] && !$blockedme && ($rules['isbyuser'] || !$permiterror_retagcat);

		if ($rules['editbutton'] && $rules['retagcatbutton']) {
			// only show one button since they lead to the same form
			if ($rules['retagcatable'] && !$rules['editable'])
				$rules['editbutton'] = false; // if we can do this without getting an error, show that as the title
			else
				$rules['retagcatbutton'] = false;
		}

		$rules['aselectable'] = $post['type'] == 'Q' && !qa_user_permit_error($rules['isbyuser'] ? null : 'permit_select_a', null, $userlevel, true, $userfields);

		$rules['flagbutton'] = qa_opt('flagging_of_posts') && !$blockedme && !$rules['isbyuser'] && !$post['hidden'] && !$post['queued']
			&& !@$post['userflag'] && !in_array($permiterror_flag, $button_errors);
		$rules['flagtohide'] = $rules['flagbutton'] && !$blockedme && !$permiterror_flag && ($post['flagcount'] + 1) >= qa_opt('flagging_hide_after');
		$rules['unflaggable'] = @$post['userflag'] && !$blockedme && !$post['hidden'];
		$rules['clearflaggable'] = $post['flagcount'] >= (@$post['userflag'] ? 2 : 1) && !$permiterror_hide_show;

		// Other actions only show the button if it's immediately possible

		$notclosedbyother = !($rules['closed'] && isset($post['closedbyid']) && !$rules['authorlast']);
		$nothiddenbyother = !($post['hidden'] && !$rules['authorlast']);

		$rules['closeable'] = qa_opt('allow_close_questions') && $post['type'] == 'Q' && !$rules['closed'] && $permiterror_close_open === false;
		// cannot reopen a question if it's been hidden, or if it was closed by someone else and you don't have global closing permissions
		$rules['reopenable'] = $rules['closed'] && isset($post['closedbyid']) && $permiterror_close_open === false && !$post['hidden']
			&& ($notclosedbyother || !qa_user_permit_error('permit_close_q', null, $userlevel, true, $userfields));

		$rules['moderatable'] = $post['queued'] && !$permiterror_moderate;
		// cannot hide a question if it was closed by someone else and you don't have global hiding permissions
		$rules['hideable'] = !$post['hidden'] && ($rules['isbyuser'] || !$post['queued']) && !$permiterror_hide_show_self
			&& ($notclosedbyother || !$permiterror_hide_show);
		// means post can be reshown immediately without checking whether it needs moderation
		$rules['reshowimmed'] = $post['hidden'] && !$permiterror_hide_show;
		// cannot reshow a question if it was hidden by someone else, or if it has flags - unless you have global hide/show permissions
		$rules['reshowable'] = $post['hidden'] && (!$permiterror_hide_show_self) &&
			($rules['reshowimmed'] || ($nothiddenbyother && !$post['flagcount']));

		$rules['deleteable'] = $post['hidden'] && !qa_user_permit_error('permit_delete_hidden', null, $userlevel, true, $userfields);
		$rules['claimable'] = !isset($post['userid']) && isset($userid) && strlen(@$post['cookieid']) && (strcmp(@$post['cookieid'], $cookieid) == 0)
			&& !($post['basetype'] == 'Q' ? $permiterror_post_q : ($post['basetype'] == 'A' ? $permiterror_post_a : $permiterror_post_c));
		$rules['followable'] = ($post['type'] == 'A' ? qa_opt('follow_on_as') : false)  && !$blockedme;

		// Check for claims that could break rules about self answering and multiple answers

		if ($rules['claimable'] && $post['basetype'] == 'A') {
			if (!qa_opt('allow_self_answer') && isset($parentpost) && qa_post_is_by_user($parentpost, $userid, $cookieid))
				$rules['claimable'] = false;

			if (isset($siblingposts) && !qa_opt('allow_multi_answers')) {
				foreach ($siblingposts as $siblingpost) {
					if ($siblingpost['parentid'] == $post['parentid'] && $siblingpost['basetype'] == 'A' && qa_post_is_by_user($siblingpost, $userid, $cookieid))
						$rules['claimable'] = false;
				}
			}
		}

		// Now make any changes based on the child posts

		if (isset($childposts)) {
			foreach ($childposts as $childpost) {
				if ($childpost['parentid'] == $post['postid']) {
					// this post has comments
					$rules['deleteable'] = false;

					if ($childpost['basetype'] == 'A' && qa_post_is_by_user($childpost, $userid, $cookieid)) {
						if (!qa_opt('allow_multi_answers'))
							$rules['answerbutton'] = false;

						if (!qa_opt('allow_self_answer'))
							$rules['claimable'] = false;
					}
				}

				if ($childpost['closedbyid'] == $post['postid']) {
					// other questions are closed as duplicates of this one
					$rules['deleteable'] = false;
				}
			}
		}

		// Return the resulting rules

		return $rules;
	}

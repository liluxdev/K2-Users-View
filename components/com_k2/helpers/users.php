<?php
/**
 * @version             1..0.4 2011-08-05
 * @package             K2 - Users view
 * @author              Olivier Nolbert http://www.jiliko.net
 * @copyright           Copyright (c) 2009 - 2011 jiliko.net.
 * @license             GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class K2HelperUsers {

	function getFirstLetters($params) {

		$user = &JFactory::getUser();
        $aid = $user->get('aid');
		$db = &JFactory::getDBO();
		
		$jnow = &JFactory::getDate();
		$now = $jnow->toMySQL();
		$nullDate = $db->getNullDate();
		
		$id = JRequest::getInt('id');
		
		$firstLetters = array();
		$query = "SELECT UPPER(LEFT(ju.name,1)) AS firstLetter
			FROM #__k2_users AS u
			LEFT JOIN #__users ju ON u.userID = ju.id
			LEFT JOIN #__k2_user_groups AS g ON g.id = u.group
			WHERE ju.block=0 AND u.group ";
	
		if (is_array($params->get('userGroups'))) {
			$query.= "IN (".implode(',',$params->get('userGroups')).")";
		}
		else {
			$query.= "= ".$db->Quote($params->get('userGroups'));
		}
		
		$query.=" AND UPPER(LEFT(ju.name, 1)) IN (".K2HelperUsers::quotedLetters($params->get('indexLetters')).")";
		
		$query.= " GROUP BY firstLetter";
		
		$db->setQuery($query);
		$firstLetters = $db->loadResultArray();
		
		$query = "SELECT COUNT(*) FROM #__k2_users AS u
			LEFT JOIN #__users ju ON u.userID = ju.id
			LEFT JOIN #__k2_user_groups AS g ON g.id = u.group
			WHERE ju.block=0 AND u.group ";
	
		if (is_array($params->get('userGroups'))) {
			$query.= "IN (".implode(',',$params->get('userGroups')).")";
		}
		else {
			$query.= "= ".$db->Quote($params->get('userGroups'));
		}
		
		$query.=" AND UPPER(LEFT(ju.name, 1)) NOT IN (".K2HelperUsers::quotedLetters($params->get('indexLetters')).")";
		
		$db->setQuery($query);
		$result = $db->loadResult();
		
		if ($result > 0)
			$firstLetters[] = "special";
			
		return $firstLetters;
	}
	
	function quotedLetters($letters) {

		$db = &JFactory::getDBO();
		
		$letters = explode(',',$letters);
		
		foreach ($letters as & $letter) {
				$letter = $db->Quote($letter);
		}
		
		return implode(',',$letters);
	}

} // End Class

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

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

class K2ModelUsers extends JModel {

    function getData($ordering = NULL) {
    
		$db = &JFactory::getDBO();
		$params = &JComponentHelper::getParams('com_k2');
		$limitstart = JRequest::getInt('limitstart');
		$limit = JRequest::getInt('limit');
	    
		$filterLetter = JRequest::getVar('filterLetter','all');
		
		if (JRequest::getWord('format') == 'feed')
			$limit = $params->get('feedLimit');
			
		$query = "SELECT u.id as id, u.userID as userID, ju.name as name, u.plugins as plugins, ju.email as email";
		$query.=" FROM #__k2_users as u LEFT JOIN #__users ju on u.userID=ju.id LEFT JOIN #__k2_user_groups as g on g.id = u.group WHERE ju.block=0 AND u.group ";
	
		if (is_array($params->get('userGroups'))) {
			$query.= "IN (".implode(',',$params->get('userGroups')).")";
		}
		else {
			$query.= "= ".$db->Quote($params->get('userGroups'));
		}

		switch ($filterLetter) {
			case 'all':
			break;
			case 'special' :
				$query.=" AND UPPER(LEFT(ju.name, 1)) NOT IN (".K2HelperUsers::quotedLetters($params->get('indexLetters')).")";
			break;
			default:	
				$query.=" AND UPPER(LEFT(ju.name, 1)) = '".$filterLetter."'";
        }
        
		//Set ordering
		switch ($ordering) {
	        
			case 'alpha':
				$orderby = 'ju.name';
			break;
	                
			case 'ralpha':
				$orderby = 'ju.name DESC';
			break;
	                
			case 'galpha':
				$orderby = 'g.name';
			break;
	                
			case 'ralpha':
				$orderby = 'g.name DESC';
			break;
	                
			case 'rand':
				$orderby = 'RAND()';
			break;
	                
			case 'oldest':
				$orderby = 'u.id ASC';
			break;
	                
			default:
				$orderby = 'u.id DESC';
			break;
		}
	        
		$query .= " ORDER BY ".$orderby;
	
		$db->setQuery($query, $limitstart, $limit);
		$rows = $db->loadObjectList();
	        
		return $rows;
    }
    
    function getTotal() {
    
		$db = &JFactory::getDBO();
		$params = &JComponentHelper::getParams('com_k2');
        
		$filterLetter = JRequest::getVar('filterLetter','all');
		
		$query = "SELECT COUNT(*) FROM #__k2_users as u LEFT JOIN #__users ju on u.userID=ju.id WHERE ju.block=0 AND u.group ";
	
		if (is_array($params->get('userGroups'))) {
			$query.= "IN (".implode(',',$params->get('userGroups')).")";
		} else {
			$query.= "= ".$db->Quote($params->get('userGroups'));
		}
        
    	switch ($filterLetter) {
			case 'all':
			break;
			case 'special' :
				$query.=" AND UPPER(LEFT(ju.name, 1)) NOT IN (".K2HelperUsers::quotedLetters($params->get('indexLetters')).")";
			break;
			default:	
				$query.=" AND UPPER(LEFT(ju.name, 1)) = '".$filterLetter."'";
        }
        
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
    }

	function prepareFeedUser($k2User) {

		$params = &JComponentHelper::getParams('com_k2');

		$k2User->description = '';

		if ($params->get('usersFeedImage'))
			$k2User->description.= '<div class="K2FeedImage"><img src="'.$k2User->avatar.'" alt="'.$k2User->name.'" /></div>';

		if ($params->get('usersFeedDescription')) {

			if ($params->get('usersFeedDescriptionWordLimit'))
				$k2User->profile->description=K2HelperUtilities::wordLimit($k2User->profile->description,$params->get('usersFeedDescriptionWordLimit'));
				
			$k2User->description.= '<div class="K2FeedDescription">'.$k2User->profile->description.'</div>';
		}

		return $k2User;	
	}

	function getNbPublishedItems($userID) {
		
		$db = &JFactory::getDBO();

		$query = "SELECT COUNT(*) FROM #__k2_items WHERE created_by={$userID} AND trash=0 AND published=1";
			
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
}
?>

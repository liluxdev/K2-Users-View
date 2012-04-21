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

jimport('joomla.application.component.view');

class K2ViewJlkUsers extends JView {

    function display($tpl = null) {
    
		global $mainframe;

		$params = &JComponentHelper::getParams('com_k2');
		$model = &$this->getModel('jlkusers');
		$limitstart = JRequest::getInt('limitstart');
		$view = JRequest::getWord('view');
		$user = & JFactory::getDBO();
		
		if($params->get('showAlphaIndex')) {
			$filterLetter = JRequest::getVar('filterLetter','all');
			
			$indexLetters = explode(',',$params->get('indexLetters'));
				
			$firstLetters = K2HelperJlkUsers::getFirstLetters($params);
			
			$alphaIndex = array();
			
			foreach ($indexLetters as $indexLetter) {
				$firstLetter = new JObject();
				$firstLetter->value = $indexLetter;
				$firstLetter->link = in_array($indexLetter,$firstLetters) ? JRoute::_('&filterLetter='.$indexLetter) : '';
				$firstLetter->selected = ($filterLetter == $indexLetter);
				$alphaIndex[] = $firstLetter;
			}
			
			$firstLetter = new JObject();
			
			$firstLetter->value = JText::_($params->get('specialString','#'));
			$firstLetter->link = in_array('special',$firstLetters) ? JRoute::_('&filterLetter=special') : '';
			$firstLetter->selected = ($filterLetter == 'special');
			$alphaIndex[] = $firstLetter;
			
			$firstLetter = new JObject();
			
			$firstLetter->value = JText::_('ALL');
			$firstLetter->link = JRoute::_('&filterLetter=all');	
			$firstLetter->selected = ($filterLetter == 'all');
			$alphaIndex[] = $firstLetter;
			
			$this->assignRef('alphaIndex', $alphaIndex);
		}
		
		//Set layout
		$this->setLayout('jlkusers');
                
		//Set limit
		$limit = $params->get('usersCount');

		//Set title
		$title = $params->get('page_title');
                
		//Set limit for model
		if(!$limit) $limit = 10;
			JRequest::setVar('limit', $limit);
        
		//Get ordering
		$ordering = $params->get('usersOrdering');
       
		//Get K2 users
		$k2Users = $model->getData($ordering);
        
        //Pagination
        jimport('joomla.html.pagination');
        $total = $model->getTotal();

		$pagination = new JPagination($total, $limitstart, $limit);
       	
		$itemlistModel = &$this->getModel('itemlist'); 
        
		//Prepare users
		foreach ($k2Users as & $k2User) {
			//Get K2 user profile
			$k2User->profile = $itemlistModel->getUserProfile($k2User->userID);

			//K2 User image
			$k2User->avatar = K2HelperUtilities::getAvatar($k2User->userID, $k2User->email, $params->get('feedImageSize'));	

			//K2 User link
			$k2User->link = JRoute::_(K2HelperRoute::getUserRoute($k2User->userID));

			//K2 User Nb Products
			$k2User->nbPublishedItems = $model->getNbPublishedItems($k2User->userID); 

			//User K2 plugins
			$k2User->event->K2UserDisplay = '';
            	
			if (is_object($k2User->profile) && $k2User->profile->id > 0) {
				$dispatcher = &JDispatcher::getInstance();
				JPluginHelper::importPlugin('k2');
				$results = $dispatcher->trigger('onK2UserDisplay', array(&$k2User->profile, &$params, $limitstart));
				$k2User->event->K2UserDisplay = trim(implode("\n", $results));
			}
		}

		//Set title
		$document = &JFactory::getDocument();
		$menus = &JSite::getMenu();
		$menu = $menus->getActive();
		if (is_object($menu)) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title'))
				$params->set('page_title', $title);
		} else {
			$params->set('page_title', $title);
		}
		
		$document->setTitle($params->get('page_title'));
        
		//Feed link
		$config =& JFactory::getConfig();
		$menu = &JSite::getMenu();
		$default = $menu->getDefault();
		$active =  $menu->getActive();

		if (!is_null($active) && $active->id==$default->id && $config->getValue('config.sef')){
			$link = '&Itemid='.$active->id.'&format=feed&limitstart=';
		} else {
			$link = '&format=feed&limitstart=';
		}
        
		$feed = JRoute::_($link);
		$this->assignRef('feed', $feed);
		$attribs = array('type'=>'application/rss+xml', 'title'=>'RSS 2.0');
		$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
		$attribs = array('type'=>'application/atom+xml', 'title'=>'Atom 1.0');
		$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
        
		$this->assignRef('user', $user);
		$this->assignRef('k2Users', $k2Users);
		$this->assignRef('params', $params);
		$this->assignRef('pagination', $pagination);
        
		//Look for template files in component folders
		$this->_addPath('template', JPATH_COMPONENT.DS.'templates');
		$this->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.'default');
        
		//Look for overrides in template folder (K2 template structure)
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates');        
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates'.DS.'default');
        
		//Look for overrides in template folder (Joomla! template structure)
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'default');
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2');
        	
		//Look for specific K2 theme files 
		if ($params->get('theme')) {
			$this->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.$params->get('theme'));
			$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates'.DS.$params->get('theme'));       
			$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.$params->get('theme'));
		}
        
		parent::display($tpl);
	}   
}

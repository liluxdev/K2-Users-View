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
       
        //Get items
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
		}
	
		//Feed link
		$config =& JFactory::getConfig();
		$menu = &JSite::getMenu();
		$default = $menu->getDefault();
		$active =  $menu->getActive();
		
		if (!is_null($active) && $active->id==$default->id && $config->getValue('config.sef')){
			$link = '&Itemid='.$active->id.'&format=feed&limitstart=';
		}
		else {
			$link = '&format=feed&limitstart=';
		}
        
        $feed = JRoute::_($link);
        $this->assignRef('feed', $feed);
        
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

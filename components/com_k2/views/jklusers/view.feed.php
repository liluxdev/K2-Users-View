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

class K2ViewUsers extends JView {

    function display($tpl = null) {
    
        global $mainframe;
        $params = &JComponentHelper::getParams('com_k2');
        $document = &JFactory::getDocument();
        $limitstart = JRequest::getInt('limitstart');
		$model = &$this->getModel('users');

        //Set title
        $title = $params->get('page_title');

        //Get ordering
        $ordering = $params->get('usersOrdering');

        //Get users
        $k2Users = $model->getData($ordering);

		$itemlistModel = &$this->getModel('itemlist');

        foreach ($k2Users as $k2User) {
		//Get K2 user profile
                $k2User->profile = $itemlistModel->getUserProfile($k2User->userID);

                //K2 User image
                $k2User->avatar = K2HelperUtilities::getAvatar($k2User->userID, $k2User->email, $params->get('usersFeedImageWidth'));        

                //K2 User link
                $k2User->link = JRoute::_(K2HelperRoute::getUserRoute($k2User->userID));
	
            $k2User = $model->prepareFeedUser($k2User);
            $k2User->name = $this->escape($k2User->name);
            $k2User->name = html_entity_decode($k2User->name);
            $feedItem = new JFeedItem();
            $feedItem->title = $k2User->name;
            $feedItem->link = $k2User->link;
            $feedItem->description = $k2User->description;
            
            //Add item
            $document->addItem($feedItem);
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
    }
    
}

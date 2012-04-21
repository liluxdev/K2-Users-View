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

jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'users.php');

$doc =& JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'components'.DS.'com_k2'.DS.'css'.DS.'k2_usersview.css');

class K2ControllerUsers extends JController {

	function display() {
		$format=JRequest::getWord('format','html');
		$document =& JFactory::getDocument();
		$viewType = $document->getType();
		$view = &$this->getView('users', $viewType);
		$model=&$this->getModel('itemlist');
		$view->setModel($model);
		$user = &JFactory::getUser();
		if ($user->guest){
			parent::display(true);
		}
		else {
			parent::display(false);
		}	
	}
	
}
?>

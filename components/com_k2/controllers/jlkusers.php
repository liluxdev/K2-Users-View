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
jimport('joomla.filesystem.file');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'jlkusers.php');

global $mainframe;

$doc =& JFactory::getDocument();

if (JFile::exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'css'.DS.'k2_jlkusersview.css'))
	$doc->addStyleSheet(JURI::base().'templates/'.$mainframe->getTemplate().'css/k2_jlkusersview.css');
else
	$doc->addStyleSheet(JURI::base().'components/com_k2/css/k2_jlkusersview.css');

class K2ControllerJlkUsers extends JController {

	function display() {
		$format=JRequest::getWord('format','html');
		$document =& JFactory::getDocument();
		$viewType = $document->getType();
		$view = &$this->getView('jlkusers', $viewType);
		$model=&$this->getModel('itemlist');
		$view->setModel($model);
		$user = &JFactory::getUser();
		parent::display($user->guest);
	}
	
}
?>

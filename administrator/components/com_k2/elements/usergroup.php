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

class JElementUserGroup extends JElement
{

	var	$_name = 'userGroup';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();
		$query = 'SELECT id as value, name as text FROM #__k2_user_groups g ORDER BY name';
		$db->setQuery( $query );
		$groups = $db->loadObjectList();

		return JHTML::_('select.genericlist',  $groups, ''.$control_name.'['.$name.'][]', 'class="inputbox" style="width:90%;" multiple="multiple" size="15"', 'value', 'text', $value );

	}

}

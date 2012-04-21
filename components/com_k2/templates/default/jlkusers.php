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

?>

<!-- Start K2 Users Layout -->

<div id="k2Container" class="jlkUsersView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title') && $this->params->get('page_title')!= $this->user->name): ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php endif; ?>

	<?php if($this->params->get('usersFeed')): ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('Subscribe to this RSS feed'); ?>">
			<span><?php echo JText::_('Subscribe to this RSS feed'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

	<?php if(isset($this->k2Users) && count($this->k2Users)): ?>
	
	<?php if($this->params->get('showAlphaIndex')): ?>
	<div class="jlkUserListAlphaIndex" style="text-align:center;">
		<?php foreach ($this->alphaIndex as $firstLetter): ?>
		<?php $active = ($firstLetter->link != ''); ?>
		<span class="firstLetter<?php echo $active ? " active" : ""; ?><?php echo $firstLetter->selected ? " selected" : ""; ?>" id="first-letter-<?php echo $firstLetter->value; ?>"><?php if($active && !$firstLetter->selected) :?><a href="<?php echo $firstLetter->link; ?>"><?php endif; ?><?php echo $firstLetter->value; ?><?php if($active && !$firstLetter->selected) :?></a><?php endif; ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<?php foreach($this->k2Users as $key=>$k2User): ?>
	<div class="userContainer" style="width:<?php echo number_format(100/$this->params->get('num_users_columns'), 1); ?>%;">
		<?php if ($this->params->get('usersImage') || $this->params->get('usersName') || $this->params->get('usersDescription') || $this->params->get('usersURL') || $this->params->get('usersEmail')): ?>
		<div class="userBlock">

			<?php if ($this->params->get('usersImage') && !empty($k2User->avatar)): ?>
			<img src="<?php echo $k2User->avatar; ?>" alt="<?php echo $k2User->name; ?>" style="width:<?php echo $this->params->get('usersImageWidth'); ?>px; height:auto;" />
			<?php endif; ?>
	
			<?php if ($this->params->get('usersName')): ?>
			<h4><a href="<?php echo $k2User->link; ?>"><?php echo $k2User->name; ?></a></h4>
			<?php endif; ?>
	
			<?php if ($this->params->get('usersDescription') && isset($k2User->profile->description)): ?>
			<p class="userDescription"><?php echo $k2User->profile->description; ?></p>
			<?php endif; ?>
	
			<?php if ($this->params->get('usersURL') || $this->params->get('usersEmail')): ?>
				<p class="userAdditionalInfo">
				<?php if ($this->params->get('usersURL') && isset($k2User->profile->url)): ?>
				<span class="userURL">
					<?php echo JText::_("Website URL"); ?>: <a href="<?php echo $k2User->profile->url; ?>" target="_blank"><?php echo $k2User->profile->url; ?></a>
				</span>
				<?php endif; ?>

				<?php if ($this->params->get('usersEmail')): ?>
				<span class="userEmail">
					<?php echo JText::_("E-mail"); ?>: <?php echo JHTML::_('Email.cloak', $k2User->email); ?>
				</span>
				<?php endif; ?>	
				</p>
			<?php endif; ?>
			
			<?php if($this->params->get('usersNbPublishedItems')):  ?>
				<span class="userEmail">
				 	<?php echo JText::_('# of published items'); ?>: <?php echo $k2User->nbPublishedItems; ?>
				</span>
			<?php endif; ?>

			<div class="clr"></div>
	
			<?php if ($this->params->get('usersPlugin')) echo $k2User->event->K2UserDisplay; ?>
	
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<?php endif; ?>
		
		<?php if(($key+1)%($this->params->get('num_users_columns'))==0): ?>
		<div class="clr"></div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>

	<div class="clr"></div>

	<!-- Pagination -->
	<?php if(count($this->pagination->getPagesLinks())): ?>
	<div class="k2Pagination">
		<?php if($this->params->get('usersPagination')) echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php if($this->params->get('usersPaginationResults')) echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>
	
	<?php endif; ?>

</div>

<!-- End K2 Users Layout -->

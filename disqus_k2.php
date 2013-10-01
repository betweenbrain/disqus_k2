<?php
/**
 * @version		1.2
 * @package		DISQUS for K2 Plugin (K2 plugin)
 * @author    Marek Wojtaszem - http://www.nika-foto.pl
 * @copyright	Copyright (c) 2012 Marek Wojtaszek. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_k2'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'k2plugin.php');

class plgK2Disqus_K2 extends K2Plugin {

	var $pluginName = 'disqus_k2';
	var $pluginNameHumanReadable = 'DISQUS for K2 plugin';

	function plgK2Disqus_K2( & $subject, $params) {
	parent::__construct($subject, $params); 
	jimport( 'joomla.html.parameter' );
	$this->plugin = &JPluginHelper::getPlugin('k2', 'disqus_k2');
	$this->params = new JParameter($this->plugin->params);	
	}

	function onK2BeforeDisplay ( &$item, &$params, $limitstart){
		$mainframe = &JFactory::getApplication();
		$plugin = &JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = new JParameter($plugin->params);

		$site_root = JURI::current();
		$shortname = $pluginParams->get('shortname');
		$identifier = $item->id;
		$parsedInModule = $params->get('parsedInModule');

		if ($parsedInModule == 1){
			if ($this->isK2()): 
			$output = '';
			else: 
			$output = '
				<script  type="text/javascript">
				var disqus_shortname = \''.$shortname.'\';
				var disqus_url = \''.$site_root.'\';
				(function () {
					var s = document.createElement(\'script\'); s.async = true;
					s.type = \'text/javascript\';
					s.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/count.js\';
					(document.getElementsByTagName(\'HEAD\')[0] || document.getElementsByTagName(\'BODY\')[0]).appendChild(s);
					}());
				</script>
			';
			endif;

			return $output;

		} 
		
		elseif ((!$parsedInModule == 1) && ($this->isArticlePage())) {
		
			$output = '
				<script  type="text/javascript">
				var disqus_shortname = \''.$shortname.'\';
				var disqus_identifier = \''.$identifier.'\';
				var disqus_url = \''.$site_root.'\';
				(function () {
					var s = document.createElement(\'script\'); s.async = true;
					s.type = \'text/javascript\';
					s.src = \'http://'.$shortname.'.disqus.com/count.js\';
					(document.getElementsByTagName(\'HEAD\')[0] || document.getElementsByTagName(\'BODY\')[0]).appendChild(s);
					}());
				</script>
				';
				
				$document = JFactory::getDocument();
				$doctype  = $document->getType();
				
				// Only render for HTML output
				if ( $doctype == 'html' ) {
					$document->addCustomTag($output); 
				}
			}

	}


	function onBeforeRender (){
		$mainframe = &JFactory::getApplication();
		$plugin = &JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = new JParameter($plugin->params);

		$site_root = JURI::current();
		$shortname = $pluginParams->get('shortname');
		
		if ($this->isArticlePage()){
			$output = '';
			} else {
			$output = '
				<script  type="text/javascript">
				var disqus_shortname = \''.$pluginParams->get('shortname').'\';
				var disqus_url = \''.$site_root.'\';
				(function () {
					var s = document.createElement(\'script\'); s.async = true;
					s.type = \'text/javascript\';
					s.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/count.js\';
					(document.getElementsByTagName(\'HEAD\')[0] || document.getElementsByTagName(\'BODY\')[0]).appendChild(s);
					}());
				</script>
			';
			}

			$document = JFactory::getDocument();
			$doctype  = $document->getType();
			
			// Only render for HTML output
			if ( $doctype == 'html' ) {
				$document->addCustomTag($output); 
			}
	}

	function onK2CommentsCounter( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();

		$plugin = &JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = new JParameter($plugin->params);

		$site_root = $mainframe->getCfg('live_site');
		$item_link = $item->link;
		$identifier = $item->id;

		$output = '<a href= "'.$site_root.$item_link.'#disqus_thread" data-disqus-identifier="'.$identifier.'">'.JText::_('DISQUS_COMMENTS').'</a>
		';

		return $output;
	}

	function onK2CommentsBlock( &$item, &$params, $limitstart) {
		$mainframe = &JFactory::getApplication();
		
		$plugin = &JPluginHelper::getPlugin('k2', $this->pluginName);
		$pluginParams = new JParameter($plugin->params);

		$item_link = $item->link;
		$identifier = $item->id;

		if ($this->isArticlePage()): 
		$output = '<a name="itemCommentsAnchor" id="itemCommentsAnchor"></a>

					<div id="disqus_thread" class="itemComments"></div>
					<script type="text/javascript">
					var disqus_identifier = \''.$identifier.'\';
						(function() {
							var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
							dsq.src = \'http://'.$pluginParams->get('shortname').'.disqus.com/embed.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
						})();
					</script>
					
					';
		else: 
		$output = '';
		endif;
		return $output;
		
	}
	
	function isArticlePage()
	{
		$option 	 = JRequest::getVar('option');
		$view 		 = JRequest::getVar('view');
		if  ($option == 'com_k2' && $view == 'item'/*K2 Specific*/) {
			return true;
		} 
		return false;
	}
	
		function isK2()
	{
		$option 	 = JRequest::getVar('option');
		if  ($option == 'com_k2') {
			return true;
		} 
		return false;
	}



} 
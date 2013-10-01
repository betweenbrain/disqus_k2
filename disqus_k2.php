<?php
/**
 * @version        1.2
 * @package        DISQUS for K2 Plugin (K2 plugin)
 * @author         Marek Wojtaszem - http://www.nika-foto.pl
 * @copyright      Copyright (c) 2012 Marek Wojtaszek. All rights reserved.
 * @license        GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

JLoader::register('K2Plugin', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_k2' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'k2plugin.php');

class plgK2Disqus_K2 extends K2Plugin {

	var $pluginName = 'disqus_k2';
	var $pluginNameHumanReadable = 'DISQUS for K2 plugin';

	function plgK2Disqus_K2(& $subject, $params) {
		parent::__construct($subject, $params);
		jimport('joomla.html.parameter');

		$this->app     = JFactory::getApplication();
		$this->doc     = JFactory::getDocument();
		$this->doctype = $this->doc->getType();
		$this->plugin  = JPluginHelper::getPlugin('k2', $this->pluginName);
		$this->params  = new JParameter($this->plugin->params);
	}

	function onK2BeforeDisplay(&$item, &$params, $limitstart) {

		$disqusUrl      = $this->params->get('disqusUrl');
		$site_root      = JURI::current();
		$shortname      = $this->params->get('shortname');
		$identifier     = $item->id;
		$parsedInModule = $params->get('parsedInModule');

		$disqusUrl = ($disqusUrl != '') ? $disqusUrl : $site_root;

		if ($parsedInModule == 1) {
			if ($this->isK2()):
				$output = ''; else:
				$output = '
				<script  type="text/javascript">
				var disqus_shortname = \'' . $shortname . '\',
					disqus_title = \'' . $item->title . '\';
					disqus_url = \'' . $disqusUrl . '\';
					(function() {
			             var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
			             dsq.src = \'//\'' . $shortname . '\'.disqus.com/embed.js\';
			             (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
			        })();
				</script>
			';
			endif;

			return $output;
		} elseif ((!$parsedInModule == 1) && ($this->isArticlePage())) {

			$output = '
				<script  type="text/javascript">
				var disqus_shortname = \'' . $shortname . '\',
					disqus_identifier = \'' . $identifier . '\',
					disqus_title = \'' . $item->title . '\',
					disqus_url = \'' . $disqusUrl . '\';
				(function () {
					(function() {
			             var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
			             dsq.src = \'//\'' . $shortname . '\'.disqus.com/embed.js\';
			             (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
			        })();
				</script>
				';

			// Only render for HTML output
			if ($this->doctype == 'html') {
				$this->doc->addCustomTag($output);
			}
		}
	}

	function onK2CommentsCounter(&$item, &$params, $limitstart) {

		$output = '<a href= "' . JURI::base() . $item->link . '#disqus_thread" data-disqus-identifier="' . $item->id . '">' . JText::_('DISQUS_COMMENTS') . '</a>';

		return $output;
	}

	function onK2CommentsBlock(&$item, &$params, $limitstart) {

		if ($this->isArticlePage()):
			$output = '<a name="itemCommentsAnchor" id="itemCommentsAnchor"></a>

					<div id="disqus_thread" class="itemComments"></div>
					<script type="text/javascript">
					var disqus_identifier = \'' . $item->id . '\';
						(function() {
							var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
							dsq.src = \'http://' . $this->params->get('shortname') . '.disqus.com/embed.js\';
							(document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
						})();
					</script>
					
					'; else:
			$output = '';
		endif;

		return $output;
	}

	function isArticlePage() {
		$option = JRequest::getVar('option');
		$view   = JRequest::getVar('view');
		if ($option == 'com_k2' && $view == 'item') {
			return TRUE;
		}

		return FALSE;
	}

	function isK2() {
		$option = JRequest::getVar('option');
		if ($option == 'com_k2') {
			return TRUE;
		}

		return FALSE;
	}
}
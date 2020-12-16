<?php
___phpbanner___;

jimport( 'joomla.plugin.plugin');
jimport( 'joomla.html.parameter');

class plgSystemWi_vars extends JPlugin
{
	protected $vars;
	protected $activeMenuItem;
	protected $reservedWords = array('if','endif');

	public function __construct(&$subject, $config = array())
	{
		$rt = parent::__construct($subject, $config);


		if(file_exists(__DIR__.'/custom/vars.php')) {
			include_once __DIR__.'/custom/vars.php';

			if(method_exists('plgSystemWi_varsHelper','setParams')) {
				plgSystemWi_varsHelper::setParams($this->params);
			}

		}

		if(file_exists(__DIR__.'/custom/vars.xml') && file_exists(__DIR__.'/wi_vars_content.php')) {
			// find extension_id which is needed for content plugin
			try {

				$extension_id = -1;
				$db = JFactory::GetDbo();
				$db->setQuery('SELECT extension_id FROM #__extensions
							WHERE type="plugin" AND element="wi_vars" AND folder="system" LIMIT 1');
				$extension_id = (int)$db->loadResult();

			} catch (Error $e) {}

			if($extension_id) {

				require_once __DIR__.'/wi_vars_content.php';
				$plgWiVarsContent = new plgContentWi_vars_content($subject,array('type'=>'content','name'=>'wi_vars_content','params'=>'{}'));
				// $plgWiVarsContent = JPluginHelper::GetPlugin('content','wi_vars_content');
				$plgWiVarsContent->_wiVarsExtensionId = $extension_id;

			}
		}

		return $rt;
	}

    function onAfterRender()
    {
		// process content on site only!!!
		if(!JFactory::GetApplication()->isAdmin()) {

			$this->_setupVars();
	        $doc = JResponse::getBody();
	        JResponse::setBody($this->replaceVars($doc));

		}
        return true;
    }

    protected function _setupVars()
    {

		$app = JFactory::GetApplication();

		$this->vars = array (
			'sitename' => JFactory::getConfig()->get('sitename',''),
			'baseURI' => JURI::base(),
			'year' => JFactory::getDate()->format('Y'),
			'date' => JFactory::getDate()->format('d.m.Y'),
			'time' => JFactory::getDate()->format('h:i'),
			'datetime' => JFactory::getDate()->format('d.m.Y - h:i'),
		);

		if(method_exists('plgSystemWi_varsHelper','getVars')) {
			$customVars = plgSystemWi_varsHelper::getVars();
			$this->vars = array_merge($this->vars,$customVars);
		}

		$this->activeMenuItem = $app->getMenu()->getActive();
		if(!$this->activeMenuItem) $this->activeMenuItem = $app->getMenu()->getDefault();

    }

    protected function replaceVars($content)
    {

		if(method_exists('plgSystemWi_varsHelper','replaceVars')) {
			$content = plgSystemWi_varsHelper::replaceVars($content);
		}

		$content = $this->_processContent($content);

		return $content;

    }

    protected function _processContent($content)
    {

        $content = preg_split(
            '/({{wi_var:[^}}]*}})/U',
            $content,
			-1,
			PREG_SPLIT_DELIM_CAPTURE
        );

		$i = 0;
		$contentLength = count($content);
		while($i<$contentLength) {
			$part = trim($content[$i]);
			if(substr($part,0,2)=='{{') {
				// part is a var expression. process it.
				$command = explode(':',substr($part,9,-2));
				if(in_array($command[0],$this->reservedWords)) {
					switch($command[0]) {
						case 'if':
							// var_dump('if',$command);
							$content[$i] = '';
							array_shift($command);
							$var = $this->_getVar($command);
							// var_dump($var);
							if(strlen($var)==0) {
								// expression is false, iterate and remove content until endif.
								// run second loop until endif expression is found!
								$i++;
								while($i<$contentLength) {
									$part = trim($content[$i]);
									$content[$i] = '';
									if($part == '{{wi_var:endif}}') {
										break;
									} else {
										$i++;
									}
								}
							}
						break;
						case 'endif':
							// endif tags should be found within the if iteration loop above.
							// if a endif is matched cause there's no matching if, just remove it.
							$content[$i] = '';
						break;
					}
				} else {
					$content[$i] = $this->_getVar($command);
				}
			}
			$i++;
		}

		// var_dump($content);
		// die;

		return implode($content);

    }

	protected function _getVar($command) {
		if(isset($this->vars[$command[0]])) {
			// auto var ?
			return $this->vars[$command[0]];

		} else if (method_exists('plgSystemWi_varsHelper','processVar_'.$command[0])) {
			// custom function ?
			$fctname = 'processVar_'.$command[0];
			array_shift($command);
			return call_user_func_array(array('plgSystemWi_varsHelper',$fctname),$command);

		} else if ($this->params->exists('var_'.$command[0])) {
			// xml param ?
			return $this->params->get('var_'.$command[0]);

		} else if(count($command)>1) {
			// 2-part param ?
			switch($command[0]) {
				case 'menuitem':
					if($command[1]=='alias') return $this->activeMenuItem->alias;
					if($command[1]=='title') return $this->activeMenuItem->params->get('page_heading',$this->activeMenuItem->title) ;
			        else return $this->activeMenuItem->params->get($command[1],'');
				break;
				default:
					return 'Missing wi_var '.implode(':',$command);
				break;
			}

		} else {
			return 'Missing wi_var '.implode(':',$command);
		}

	}

}
?>

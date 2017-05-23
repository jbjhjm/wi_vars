<?php
<%= phpbanner %>

class plgContentWi_vars_content extends JPlugin {

	public function __construct(&$subject, $config = array())
	{
		$rt = parent::__construct($subject, $config);
		$this->_wiVarsExtensionId = -1;
		return $rt;
	}

    function onContentPrepareForm($form, $data) {

        $app = JFactory::getApplication();
        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $extId = (int)$app->input->get('extension_id');

		// var_dump($app->input);
		// var_dump($this->_wiVarsExtensionId);

        if ($option == 'com_plugins' && $extId == $this->_wiVarsExtensionId && $app->isAdmin()) {
	        if (file_exists(__DIR__ . '/custom/vars.xml')) {
	            JForm::addFormPath(__DIR__ . '/custom');
	            $form->loadFile('vars', false);
	        }
        }

        return true;

    }

}

?>

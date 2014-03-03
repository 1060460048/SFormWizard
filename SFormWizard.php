<?php
/**
 * jQuery Form Wizard v3.0.7 - Yii extension
 *
 *******************************************
 * options:
 *	historyEnabled
 *	Enables the BBQ plugin
 *	Type boolean
 *	Default value: false
 *	Description true enables the BBQ plugin. Enables navigation of the wizard using the browser's back and forward buttons
 *
 *	validationEnabled
 *	Enables the validation plugin
 *	Type boolean
 *	Default value: false
 *	Description true enables the validation plugin.
 *
 *	validationOptions
 *	Holds options for the validation plugin
 *	Type Object
 *	Default value: undefined
 *	Description Holds options for the validation plugin. See validation plugin documentation for specific options.
 *	RequiresvalidationEnabled
 *
 *	formPluginEnabled
 *	Enables the form plugin
 *	Type boolean
 *	Default value: false
 *	Description true enables the form plugin. Makes sure that the plugin is posted via AJAX. Set to false if you want to post the form without using AJAX.
 *
 *	formOptions
 *	Holds options for the form plugin
 *	Type Object
 *	Default value: { reset: true, success: function(data) { alert("success"); }
 *	Description  Holds options for the form plugin. See form plugin documentation for specific options.
 *
 *	linkClass
 *	CSS-class of inputs used as links in the wizard
 *	Type String (selector)
 *	Default value: ".link"
 *	Description Specifies the CSS-class of inputs used as links in the wizard.
 *
 *	submitStepClass
 *	CSS-class of steps where the form should be submitted
 *	Type String
 *	Default value: "submit_step"
 *	Description Specifies the CSS-class of the steps where the form should be submitted.
 *
 *	back
 *	Elements used as back buttons
 *	Type String (selector)
 *	Default value: ":reset"
 *	Description Specifies the elements used as back buttons
 *
 *	next
 *	Elements used as next buttons
 *	Type String (selector)
 *	Default value: ":submit"
 *	Description Specifies the elements used as next buttons
 *
 *	textSubmit
 *	The text of the next button on submit steps
 *	Type String
 *	Default value: 'Submit'
 *	Description The text of the next button on submit steps.
 *
 *	textNext
 *	The text of the next button on non-submit steps
 *	Type String
 *	Default value: 'Next'
 *	Description The text of the next button on non-submit steps.
 *
 *	textBack
 *	The text of the back button
 *	Type String
 *	Default value: 'Back'
 *	Description The text of the back button.
 *
 *	remoteAjax
 *	Object holding options for AJAX calls done between steps
 *	Type Object
 *	Default value: undefined
 *	Description Object holding options for AJAX calls done between steps
 *
 *	inAnimation
 *	The animation done during the in-transition between steps
 *	Type Object
 *	Default value: {opacity: 'show'}
 *	Description Specifies the animation done during the in-transition between steps
 *
 *	outAnimation
 *	The animation done during the out-transition between steps
 *	Type Object
 *	Default value: {opacity: 'hide'}
 *	Description Specifies the animation done during the out-transition between steps
 *
 *	inDuration
 *	The duration of the in-animation between steps
 *	Type Number
 *	Default value: 400
 *	Description Specifies the duration of the in-animation between steps
 *
 *	outDuration
 *	The duration of the out-animation between steps
 *	Type Number
 *	Default value: 400
 *	Description Specifies the duration of the out-animation between steps
 *
 *	easing
 *	The easing used during the transition animations between steps
 *	Type String
 *	Default value: 'swing'
 *	Description Specifies the easing used during the transition animations between steps. See jQuery Easing Plugin documentation for more information on easings.
 *
 *	focusFirstInput
 *	True means that the first input field on each step should be focused
 *	Type boolean
 *	Default value: false
 *	Description Specifies whether the first input field on each step should be focused.
 *
 *	disableInputFields
 *	True means that the input fields in the form should be disabled
 *	Type boolean
 *	Default value: true
 *	Description Specifies whether the input fields in the form should be disabled during the initialization of the plugin. The disabling of inputs may be needed to be done in HTML if the number of input fields are very large, if this is needed - set this flag to false. 
 *
 *	disableUIStyles
 *	True means that the wizard will not set any jquery UI styles
 *	Type boolean
 *	Default value: false
 *	Description Specifies whether the wizard should use jquery UI styles or not.
 *
 */
class SFormWizard extends CWidget
{
	public $selector;
	public $jsAfterStepShown;

	public $historyEnabled = false;
	public $formPluginEnabled = false;
	public $validationEnabled = false;

	public $formOptions = '{ reset: "true"}, success: function(data) { alert("success"); }';
	public $validationOptions = 'undefined';

	public $linkClass = '.link';
	public $submitStepClass = '.submit_step';
	public $back = ':reset';
	public $next = ':submit';
	public $textSubmit = 'Submit';
	public $textNext = 'Next';
	public $textBack = 'Back';
	public $remoteAjax = 'undefined';
	public $inAnimation = "{opacity:'show'}";
	public $outAnimation = "{opacity:'hide'}";
	public $inDuration = 400;
	public $outDuration = 400;
	public $easing = 'swing';
	public $focusFirstInput = false;
	public $disableInputFields = true;
	public $disableUIStyles = true;

	/**
	 * Publishes the required assets
	 */
	public function init() {
		parent::init();
	}

	/**
	 * Run the widget.
	 */
	public function run() {

		// check booleans
		$this->historyEnabled = self::parseBool($this->historyEnabled);
		$this->formPluginEnabled = self::parseBool($this->formPluginEnabled);
		$this->validationEnabled = self::parseBool($this->validationEnabled);
		$this->focusFirstInput = self::parseBool($this->focusFirstInput);
		$this->disableInputFields = self::parseBool($this->disableInputFields);
		$this->disableUIStyles = self::parseBool($this->disableUIStyles);

		$this->publishAssets();
	}

	/**
	 * Publises and registers the required CSS and Javascript
	 * @throws CHttpException if the assets folder was not found
	 */
	public function publishAssets() {
		$assetsDir = dirname(__FILE__).'/assets';
		if (!is_dir($assetsDir)) {
			throw new CHttpException(500, __CLASS__ . ' - Error: Couldn\'t find assets to publish.');
		}
		$assets = Yii::app()->assetManager->publish($assetsDir);

		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery.ui');
		if ($this->historyEnabled)
			$cs->registerCoreScript('bbq');

		// js dependencies
		$ext = defined('YII_DEBUG') && YII_DEBUG ? 'js' : 'min.js';
		if ($this->formPluginEnabled)
			$cs->registerScriptFile($assets.'/js/jquery.form.'.$ext, CClientScript::POS_END);
		if ($this->validationEnabled)
			$cs->registerScriptFile($assets.'/js/jquery.validate.'.$ext, CClientScript::POS_END);

		$cs->registerScriptFile($assets.'/js/jquery.form.wizard.'.$ext, CClientScript::POS_END);

		$cs->registerScript('initformwizard','
		$(function(){
			$("'.$this->selector.'").formwizard({
				historyEnabled: '.($this->historyEnabled ? 'true':'false').',
				formPluginEnabled: '.($this->formPluginEnabled ? 'true':'false').',
				validationEnabled: '.($this->validationEnabled ? 'true':'false').',

				formOptions: '.$this->formOptions.',
				validationOptions: '.$this->validationOptions.',

				linkClass: '.CJavascript::encode($this->linkClass).',
				submitStepClass: '.CJavascript::encode($this->submitStepClass).',
				back: '.CJavascript::encode($this->back).',
				next: '.CJavascript::encode($this->next).',
				textSubmit: '.CJavascript::encode($this->textSubmit).',
				textNext: '.CJavascript::encode($this->textNext).',
				textBack: '.CJavascript::encode($this->textBack).',
				remoteAjax: '.$this->remoteAjax.',
				inAnimation: '.$this->inAnimation.',
				outAnimation: '.$this->outAnimation.',
				inDuration: '.CJavascript::encode($this->inDuration).',
				outDuration: '.CJavascript::encode($this->outDuration).',
				easing: '.CJavascript::encode($this->easing).',
				focusFirstInput: '.($this->focusFirstInput ? 'true':'false').',
				disableInputFields: '.($this->disableInputFields ? 'true':'false').',
				disableUIStyles: '.($this->disableUIStyles ? 'true':'false').',
			});

			$("#stepmessage").append($("'.$this->selector.'").formwizard("state").firstStep);

			$("'.$this->selector.'").bind("step_shown", function(event, data) {
				'.$this->jsAfterStepShown.';
			});

		});', CClientScript::POS_END);
	}

	/**
	 * Allow boolean options as text or numeric
	 */
	protected static function parseBool($data)
	{
		if (is_bool($data) || is_numeric($data))
			return $data;

		switch (strtolower($data)) {
			case 'false':
				return false;
			case 'true':
				return true;
			default:
				return $data;
		}
	}

}

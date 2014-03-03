<?php

 /** options
	historyEnabled
	Enables the BBQ plugin
	Typeboolean
	Default value: false
	Descriptiontrue enables the BBQ plugin. Enables navigation of the wizard using the browser's back and forward buttons

	validationEnabled
	Enables the validation plugin
	Typeboolean
	Default value: false
	Descriptiontrue enables the validation plugin.

	validationOptions
	Holds options for the validation plugin
	TypeObject
	Default value: undefined
	DescriptionHolds options for the validation plugin. See validation plugin documentation for specific options.
	RequiresvalidationEnabled

	formPluginEnabled
	Enables the form plugin
	Typeboolean
	Default value: false
	Descriptiontrue enables the form plugin. Makes sure that the plugin is posted via AJAX. Set to false if you want to post the form without using AJAX.

	formOptions
	Holds options for the form plugin
	TypeObject
	Default value: { reset: true, success: function(data) { alert("success"); }
	Description Holds options for the form plugin. See form plugin documentation for specific options.

	linkClass
	CSS-class of inputs used as links in the wizard
	TypeString (selector)
	Default value: ".link"
	DescriptionSpecifies the CSS-class of inputs used as links in the wizard.

	submitStepClass
	CSS-class of steps where the form should be submitted
	TypeString
	Default value: "submit_step"
	DescriptionSpecifies the CSS-class of the steps where the form should be submitted.
	
	back
	Elements used as back buttons
	TypeString (selector)
	Default value: ":reset"
	DescriptionSpecifies the elements used as back buttons

	next
	Elements used as next buttons   	
	TypeString (selector)
	Default value: ":submit"
	DescriptionSpecifies the elements used as next buttons

	textSubmit
	The text of the next button on submit steps
	TypeString
	Default value: 'Submit'
	DescriptionThe text of the next button on submit steps.
	
	textNext
	The text of the next button on non-submit steps
	TypeString
	Default value: 'Next'
	DescriptionThe text of the next button on non-submit steps.

	textBack
	The text of the back button
	TypeString
	Default value: 'Back'
	DescriptionThe text of the back button.

	remoteAjax
	Object holding options for AJAX calls done between steps
	TypeObject
	Default value: undefined
	DescriptionObject holding options for AJAX calls done between steps
	
	inAnimation
	The animation done during the in-transition between steps
	TypeObject
	Default value: {opacity: 'show'}
	DescriptionSpecifies the animation done during the in-transition between steps

	outAnimation
	The animation done during the out-transition between steps
	TypeObject
	Default value: {opacity: 'hide'}
	DescriptionSpecifies the animation done during the out-transition between steps

	inDuration
	The duration of the in-animation between steps   
	TypeNumber
	Default value: 400
	DescriptionSpecifies the duration of the in-animation between steps
	
	outDuration
	The duration of the out-animation between steps
	TypeNumber
	Default value: 400
	DescriptionSpecifies the duration of the out-animation between steps


	easing
	The easing used during the transition animations between steps
	TypeString
	Default value: 'swing'
	DescriptionSpecifies the easing used during the transition animations between steps. See jQuery Easing Plugin documentation for more information on easings.


	focusFirstInput
	True means that the first input field on each step should be focused
	Typeboolean
	Default value: false
	DescriptionSpecifies whether the first input field on each step should be focused.

	disableInputFields
	True means that the input fields in the form should be disabled
	Typeboolean
	Default value: true
	DescriptionSpecifies whether the input fields in the form should be disabled during the initialization of the plugin. The disabling of inputs may be needed to be done in HTML if the number of input fields are very large, if this is needed - set this flag to false. 

	disableUIStyles
	True means that the wizard will not set any jquery UI styles
	Typeboolean
	Default value: false
	DescriptionSpecifies whether the wizard should use jquery UI styles or not. 

 **/
class SFormWizard extends CWidget
{
	public $selector;
	public $jsAfterStepShown;

	public $historyEnabled = 'false';
	public $formPluginEnabled = 'false';
	public $validationEnabled = 'false';
	public $validationOptions = 'undefined';

	public $formOptions = '{ reset: "true"}, success: function(data) { alert("success"); }';
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
	public $focusFirstInput = 'false';
	public $disableInputFields = 'true';
	public $disableUIStyles = 'true';

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
				historyEnabled: '.$this->historyEnabled.',
				formPluginEnabled: '.$this->formPluginEnabled.',
				validationEnabled: '.$this->validationEnabled.',

				formOptions: '.$this->formOptions.',
				validationOptions: '.$this->validationOptions.',

				linkClass: '.CJavascript::encode($this->linkClass).',
				submitStepClass: '.CJavascript::encode($this->submitStepClass).',
				back: '.CJavascript::encode($this->back).',
				next: '.CJavascript::encode($this->next).',
				textSubmit: '.CJavascript::encode($this->textSubmit).',
				textNext: '.CJavascript::encode($this->textNext).',
				textBack: '.CJavascript::encode($this->textBack).',
				remoteAjax: '.CJavascript::encode($this->remoteAjax).',
				inAnimation: '.$this->inAnimation.',
				outAnimation: '.$this->outAnimation.',
				inDuration: '.CJavascript::encode($this->inDuration).',
				outDuration: '.CJavascript::encode($this->outDuration).',
				easing: '.CJavascript::encode($this->easing).',
				focusFirstInput: '. $this->focusFirstInput.',
				disableInputFields: '.$this->disableInputFields.',
				disableUIStyles: '.$this->disableUIStyles.',
			});

			$("#stepmessage").append($("'.$this->selector.'").formwizard("state").firstStep);

			$("'.$this->selector.'").bind("step_shown", function(event, data) {
				'.$this->jsAfterStepShown.';
			});

		});', CClientScript::POS_END);
	}

}

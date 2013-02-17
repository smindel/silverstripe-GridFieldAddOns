<?php

class GridFieldExpandableForm implements GridField_URLHandler, GridField_HTMLProvider {

	public $template = 'GridFieldExpandableForm';
	public $formorfields;

	function __construct($formorfields = null) {

		$this->formorfields = $formorfields;
	}

	public function getURLHandlers($gridField) {
		return array(
			'expand/$ID' => 'handleItem',
		);
	}

	public function handleItem($gridField, $request) {

		$controller = $gridField->getForm()->Controller();

		$record = $gridField->getList()->byId($request->param("ID"));

		$handler = Object::create('GridFieldExpandableForm_ItemRequest', $gridField, $this, $record, $controller, 'DetailForm', $this->formorfields);

		return $handler->handleRequest($request, DataModel::inst());
	}

	public function getHTMLFragments($gridField) {

		Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-ui/jquery-ui.js');
		Requirements::javascript('GridFieldAddOns/javascript/GridFieldExpandableForm.js');
		Requirements::css('GridFieldAddOns/css/GridFieldExpandableForm.css');

		$gridField->addExtraClass('expandable-forms');
		$gridField->setAttribute('data-pseudo-form-url', $gridField->Link('expand'));

		return array();
	}

}

class GridFieldExpandableForm_ItemRequest extends RequestHandler {

	static $url_handlers = array(
		'$Action!' => '$Action',
		'' => 'edit',
	);

	protected $gridfield;
	protected $component;
	protected $record;
	protected $controller;
	protected $name;
	protected $formorfields;
	protected $template = 'GridFieldExpandableForm';

	public function __construct($gridfield, $component, $record, $controller, $name, $formorfields) {
		$this->gridfield = $gridfield;
		$this->component = $component;
		$this->record = $record;
		$this->controller = $controller;
		$this->name = $name;
		$this->formorfields = $formorfields;
		parent::__construct();
	}

	public function edit($request) {
		$controller = $this->getToplevelController();
		$form = $this->ExpandableForm($this->gridField, $request);

		return $this->customise(array(
			'ExpandableForm' => $form,
		))->renderWith($this->template);
	}

	public function ExpandableForm() {

		if($this->formorfields instanceof FieldList) {
			$fields = $this->formorfields;
		} else if($this->formorfields instanceof ViewableData) {
			$form = $this->formorfields;
		} else if($this->record->hasMethod('getExandableFormFields')) {
			$fields = $this->record->getExandableFormFields();
			$this->record->extend('updateExandableFormFields', $fields);
		} else {
			$fields = $this->record->scaffoldFormFields();
			$this->record->extend('updateExandableFormFields', $fields);
		}

		if(empty($form)) {
			$actions = new FieldList();
			$actions->push(FormAction::create('doSave', _t('GridFieldDetailForm.Save', 'Save'))
				->setUseButtonTag(true)
				->addExtraClass('ss-ui-action-constructive')
				->setAttribute('data-icon', 'accept')
				->setAttribute('data-action-type', 'default'));

			$form = new Form(
				$this,
				'ExpandableForm',
				$fields,
				$actions,
				$this->validator
			);
		}

		$form->loadDataFrom($this->record, Form::MERGE_DEFAULT);

		$form->IncludeFormTag = false;

		return $form;
	}

	public function doSave($data, $form) {
		try {
			$form->saveInto($this->record);
			$this->record->write();
			$list = $this->gridfield->getList();
			if($list instanceof ManyManyList) {
				$extradata = array_intersect_key($data, $list->getField('extraFields'));
				$list->add($this->record, $extradata);
			} else {
				$list->add($this->record);
			}
		} catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			$responseNegotiator = new PjaxResponseNegotiator(array(
				'CurrentForm' => function() use(&$form) {
					return $form->forTemplate();
				},
				'default' => function() use(&$controller) {
					return $controller->redirectBack();
				}
			));
			if($controller->getRequest()->isAjax()){
				$controller->getRequest()->addHeader('X-Pjax', 'CurrentForm');
			}
			return $responseNegotiator->respond($controller->getRequest());
		}
		return $this->customise(array('ExpandableForm' => $form))->renderWith($this->template);
	}

	public function doDelete($data, $form) {
		try {
			if (!$this->record->canDelete()) {
				throw new ValidationException(
					_t('GridFieldDetailForm.DeletePermissionsFailure',"No delete permissions"),0);
			}

			$this->record->delete();
		} catch(ValidationException $e) {
			$form->sessionMessage($e->getResult()->message(), 'bad');
			return Controller::curr()->redirectBack();
		}
		return 'deleted';
	}

	protected function getToplevelController() {
		$c = $this->popupController;
		while($c && $c instanceof GridFieldExpandableForm_ItemRequest) {
			$c = $c->getController();
		}
		return $c;
	}
	
	public function Link($action = null) {
		return Controller::join_links($this->gridfield->Link('expand'),
			$this->record->ID ? $this->record->ID : 'new', $action);
	}
}
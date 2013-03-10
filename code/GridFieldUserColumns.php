<?php

class GridFieldUserColumns extends ViewableData implements GridField_ColumnProvider, GridField_HTMLProvider, GridField_URLHandler {

	static $static_field_for_extra_columns = 'extra_summary_fields';

	protected $gridField;
	protected $default_columns;

	function init($gridField) {
		$this->gridField = $gridField;
	}

	/**
	 * This component is not providing columns but manipulating the columns provided by GridFieldDataColumns.
	 * 
	 * @param GridField $gridField
	 * @param array - List reference of all column names.
	 */
	public function augmentColumns($gridField, &$columns) {

		$this->init($gridField);

		Requirements::javascript('GridFieldAddOns/javascript/GridFieldUserColumns.js');

		$usercolumns = $this->currentColumns();
		$extracolumns = array_diff($columns, $this->availableColumns());
		$displaycolumns = array_values(array_unique(array_merge(array_keys($usercolumns), $extracolumns)));

		$datacolumnscomponent = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
		$datacolumnscomponent->setDisplayFields($usercolumns);

		$columns = $displaycolumns;
	}

	function defaultColumns() {

		if(!$this->default_columns) {
			if(!$this->gridField) throw new Exception('GridField not yet set. Do not call GridFieldUserColumns::defaultColumns() before GridFieldUserColumns::augmentColumns().');
			$datacolumnscomponent = $this->gridField->getConfig()->getComponentByType('GridFieldDataColumns');
			$this->default_columns = $datacolumnscomponent->getDisplayFields($this->gridField);
		}

		return $this->default_columns;
	}

	function userColumns() {

		if(!$this->gridField) throw new Exception('GridField not yet set. Do not call GridFieldUserColumns::userColumns() before GridFieldUserColumns::augmentColumns().');

		if(
			Member::currentUser()->hasField('GridFieldUserColumns') &&
			Member::currentUser()->GridFieldUserColumns &&
			($usercolumns = Member::currentUser()->getGridFieldUserColumnsFor($this->gridField->getList()->dataClass()))
		) {
			return $usercolumns;
		}
		return false;
	}

	function currentColumns() {
		$user = $this->userColumns();
		return is_array($user) ? $user : $this->defaultColumns();
	}

	function availableColumns() {

		if(!$this->gridField) throw new Exception('GridField not yet set. Do not call GridFieldUserColumns::userColumns() before GridFieldUserColumns::augmentColumns().');

		$class = $this->gridField->getList()->dataClass();
		$default = $this->defaultColumns();
		$user = $this->userColumns();
		$extra = Config::inst()->get($class, self::$static_field_for_extra_columns);
		$default = is_array($extra) ? array_merge($default, $extra) : $default;

		return is_array($user) ? array_merge($user, $default) : $default;
	}

	// since we're not really provide columns we're not returning anything
	public function getColumnsHandled($gridField) { return array(); }
	public function getColumnMetadata($gridField, $columnName) { return array(); }
	public function getColumnContent($gridField, $record, $columnName) { return false; }
	public function getColumnAttributes($gridField, $record, $columnName) { return array(); }

	/**
	 * Returns a map where the keys are fragment names and the values are pieces of HTML to add to these fragments.
	 *
	 * Here are 4 built-in fragments: 'header', 'footer', 'before', and 'after', but components may also specify
	 * fragments of their own.
	 * 
	 * To specify a new fragment, specify a new fragment by including the text "$DefineFragment(fragmentname)" in the
	 * HTML that you return.  Fragment names should only contain alphanumerics, -, and _.
	 *
	 * If you attempt to return HTML for a fragment that doesn't exist, an exception will be thrown when the GridField
	 * is rendered.
	 *
	 * @return Array
	 */
	public function getHTMLFragments($gridField) {

		$gridField->getList()->dataClass();
		$buttonlabel = _t('GridFieldAddOns.ChangeColumns',"Change Columns");

		return array(
			'buttons-before-right' => "<a class=\"action action ss-ui-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary gridfield-setup\" data-icon=\"settings\">$buttonlabel</a>",
		);
	}

	public function getURLHandlers($gridField) {
		return array(
			'usercolumnsform' => 'UserColumnsForm',
			'saveusercolumns' => 'saveUserColumns',
		);
	}

	function UserColumnsForm($gridField, $request) {
		$this->init($gridField);
		return $this->renderWith('GridFieldUserColumns_Form');
	}

	function Columns() {
		$available = $this->availableColumns();
		$current = $this->currentColumns();

		$columns = new ArrayList();
		foreach($available as $key => $val) {
			$selected = array_search($val, $current) !== false;
			$columns->push(new ArrayData(array('Name' => $key, 'Title' => $val, 'Selected' => $selected)));
		}
		return $columns;
	}

	function saveUserColumns($gridField, $request) {
		$this->init($gridField);
		$available = $this->availableColumns();
		$postcolumns = $request->postVar('columns');
		$newcolumns = array();
		if(!is_array($postcolumns)) return json_encode('bad');
		foreach($postcolumns as $col) {
			list($name, $title) = explode(':', $col);
			if(!isset($available[$name]) || $available[$name] != $title) return json_encode(array('bad', $this->gridField->getList()->dataClass()));
			$newcolumns[$name] = $title;
		}
		Member::currentUser()->setGridFieldUserColumnsFor($gridField->getList()->dataClass(), $newcolumns);
		return json_encode('good');
	}
}

class GridFieldConfig_ExtendedRecordEditor extends GridFieldConfig_RecordEditor {

	function __construct($itemsPerPage = null) {
		parent::__construct($itemsPerPage);
		$this->addComponent(new GridFieldUserColumns());
	}
}

class GridFieldConfig_ExtendedRelationEditor extends GridFieldConfig_RelationEditor {

	function __construct($itemsPerPage = null) {
		parent::__construct($itemsPerPage);
		$this->addComponent(new GridFieldUserColumns());
	}
}
<?php

class GridFieldEditableCells implements GridField_ColumnProvider, GridField_URLHandler {

	protected $fields;
	protected $columns;

	function __construct(FieldList $fields) {
		$this->fields = $fields;

		$columns = array();
		foreach($this->fields as $field) $columns[] = $field->getName();
		$this->columns = $columns;
	}

	/**
	 * Add extra fields to the column list
	 * 
	 * @param GridField $gridField
	 * @param array - List reference of all column names.
	 */
	public function augmentColumns($gridField, &$columns) {

		// remove columns handled by GridFieldEditableCells from GridFieldDataColumns
		// @FIXME: if _all_ columns get removed from GridFieldDataColumns infact none gets removed
		$datacolumnscomponent = $gridField->getConfig()->getComponentByType('GridFieldDataColumns');
		$datacolumns = $datacolumnscomponent->getDisplayFields($gridField);
		$datacolumns = array_diff($datacolumns, $this->columns);
		$datacolumnscomponent->setDisplayFields($datacolumns);

		foreach($this->columns as $name) {
			if(!in_array($name, $columns)) {
				if(($pos = array_search('Actions', $columns)) === false) {
					$columns[] = $name;
				} else {
					array_splice($columns, $pos, $pos * -1, $name);
				}
			}
		}
	}

	/**
	 * List of handled columns
	 * 
	 * @param GridField $gridField
	 * @return array 
	 */
	public function getColumnsHandled($gridField) {

		return $this->columns;
	}

	/**
	 * Set titles for the column header
	 * 
	 * @param GridField $gridField
	 * @param string $columnName
	 * @return array - Map of arbitrary metadata identifiers to their values.
	 */
	public function getColumnMetadata($gridField, $columnName) {

		return array('title' => $columnName);
	}

	/**
	 * Return a formfield for the extra field column or an edit button for the actions column
	 * 
	 * @param  GridField $gridField
	 * @param  DataObject $record - Record displayed in this row
	 * @param  string $columnName
	 * @return string - HTML for the column. Return NULL to skip.
	 */
	public function getColumnContent($gridField, $record, $columnName) {

		Requirements::javascript('GridFieldAddOns/javascript/GridFieldEditableCells.js');
		Requirements::css('GridFieldAddOns/css/GridFieldEditableCells.css');

		$name = "{$gridField->Name}_EditableCell[{$record->ID}][{$columnName}]";
		$value = $record->has_one($columnName) ? $record->{$columnName . 'ID'} : $record->$columnName;
		$field = clone($this->fields->fieldByName($columnName));
		$field->setName($name);
		$field->setValue($value);

		return $field->Field();
	}

	/**
	 * Generate HTML attributes for each individual cells as selectors for CSS and JS
	 * 
	 * @param  GridField $gridField
	 * @param  DataObject $record displayed in this row
	 * @param  string $columnName
	 * @return array
	 */
	public function getColumnAttributes($gridField, $record, $columnName) {

		return array(
			"data-gridfield-editable-cell-column" => $columnName,
			"data-gridfield-editable-cell-dirty" => 0,
		);
	}

	/**
	 * Return URLs to be handled by this grid field, in an array the same form as $url_handlers.
	 * Handler methods will be called on the component, rather than the grid field.
	 */
	public function getURLHandlers($gridField) {
		return array(
			'savecomponent/$ID' => 'savecomponent',
		);
	}

	public function savecomponent($gridField, $request) {

		$data = $request->postVar($gridField->getName() . '_EditableCell');
		foreach($data as $id => $params) {
			$record = $gridField->getList()->byId((int)$id);
			if(!$record) return json_encode(array('type' => 'error', 'message' => 'Bad request'));
			if(!$record->canEdit()) return json_encode(array('type' => 'error', 'message' => 'Permission denied'));
			$add = array();
			foreach($params as $key => $val) {
				$val = Convert::raw2sql($val);
				if($record->hasDatabaseField($key)) {
					$record->$key = $val;
				} else if($record->has_one($key)) {
					$col = $key . 'ID';
					$record->$col = $val;
				} else if($gridField->getList() instanceof ManyManyList) {
					$extradata = $gridField->getList()->getExtraData($gridField->getName(), $record->ID);
					if(array_key_exists($key, $extradata)) $add[$key] = $val;
				}
			}
			if($record->isChanged()) {
				try {
					$record->write(true);
					return json_encode(array('type' => 'good', 'message' => 'Record saved'));
				} catch(ValidationException $e) {
					return json_encode(array('type' => 'bad', 'message' => $e->getMessage()));
				}
			} else if(count($add)) {
				$gridField->getList()->add($record, $add);
				return json_encode(array('type' => 'good', 'message' => 'Relation updated'));
			} else {
				return json_encode(array('type' => 'good', 'message' => 'Nothing chnaged'));
			}

		}
	}

}
<?php

class GridFieldRecordHighlighter implements GridField_ColumnProvider {

	protected $alerts;

	function __construct($alerts) {
		$this->alerts = $alerts;
	}

	/**
	 * Add extra fields to the column list
	 * 
	 * @param GridField $gridField
	 * @param array - List reference of all column names.
	 */
	public function augmentColumns($gridField, &$columns) {

		array_unshift($columns, 'Alerts');
	}

	/**
	 * List of handled columns
	 * 
	 * @param GridField $gridField
	 * @return array 
	 */
	public function getColumnsHandled($gridField) {

		return array('Alerts');
	}

	/**
	 * Set titles for the column header
	 * 
	 * @param GridField $gridField
	 * @param string $columnName
	 * @return array - Map of arbitrary metadata identifiers to their values.
	 */
	public function getColumnMetadata($gridField, $columnName) {

		return array('title' => '');
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

		Requirements::javascript('GridFieldAddOns/javascript/GridFieldRecordHighlighter.js');
		Requirements::css('GridFieldAddOns/css/GridFieldRecordHighlighter.css');

		$alerts = $this->getAlerts($record);

		$content = array();
		foreach($alerts as $alert) $content[] = "<span class=\"ss-gridfield-alert ui-icon ui-icon-{$alert['status']}\" title=\"{$alert['message']}\"></span>";
		
		return implode($content);
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

		$attr = array();

		foreach($this->getAlerts($record) as $alert) {
			if($alert['status'] == 'alert') {
				$attr = array('class' => 'ss-gridfield-highlight', 'data-highlight-status' => 'error');
			} else if(empty($attr)) {
				$attr = array('class' => 'ss-gridfield-highlight', 'data-highlight-status' => 'highlight');
			}
		}

		return $attr;
	}

	function getAlerts($record) {

		$alerts = array();

		foreach($this->alerts as $getter => $rule) {
			$actualvalue = $record->hasField($getter) ? $record->$getter : $record->$getter();
			foreach($rule['patterns'] as $nominalvalue => $return) {
				if(
					($rule['comparator'] == 'equal' &&			$actualvalue == $nominalvalue) ||
					($rule['comparator'] == 'equalstrict' &&	$actualvalue === $nominalvalue) ||
					($rule['comparator'] == 'unequal' &&		$actualvalue != $nominalvalue) ||
					($rule['comparator'] == 'unequalstrict' &&	$actualvalue !== $nominalvalue) ||
					($rule['comparator'] == 'greater' &&		$actualvalue > $nominalvalue) ||
					($rule['comparator'] == 'greaterorequal' &&	$actualvalue >= $nominalvalue) ||
					($rule['comparator'] == 'less' &&			$actualvalue < $nominalvalue) ||
					($rule['comparator'] == 'lessorequal' &&	$actualvalue <= $nominalvalue) ||
					($rule['comparator'] == 'beginwith' &&		strtolower(substr($actualvalue, 0, strlen($nominalvalue))) == strtolower($nominalvalue)) ||
					($rule['comparator'] == 'endwith' &&		strtolower(substr($actualvalue, -1 * strlen($nominalvalue))) == strtolower($nominalvalue)) ||
					($rule['comparator'] == 'contain' &&		stripos($actualvalue, $nominalvalue) !== false) ||
					($rule['comparator'] == 'regex' &&			preg_match($nominalvalue, $actualvalue))
				) {
					$alerts[$getter] = array(
						'status' => $return['status'],
						'message' => sprintf($return['message'], Convert::raw2xml($nominalvalue), Convert::raw2xml($actualvalue)),
					);
					break;
				}
			}
		}

		return $alerts;
	}
}
<?php

class GridFieldUserColumnsExtension extends DataExtension {

	static $db = array(
		'GridFieldUserColumns' => 'Text',
	);

	function getGridFieldUserColumnsFor($gridfielddataclass) {
		if(!$this->owner->GridFieldUserColumns) return false;
		$columns = unserialize($this->owner->GridFieldUserColumns);
		return isset($columns[$gridfielddataclass]) ? $columns[$gridfielddataclass] : false;
	}

	function setGridFieldUserColumnsFor($gridfielddataclass, $newcolumns) {
		$columns = $this->owner->GridFieldUserColumns ? unserialize($this->owner->GridFieldUserColumns) : array();
		$columns[$gridfielddataclass] = $newcolumns;
		$this->owner->GridFieldUserColumns = serialize($columns);
		$this->owner->write();
	}
}
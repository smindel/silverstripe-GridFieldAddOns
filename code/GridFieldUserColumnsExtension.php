<?php

class GridFieldUserColumnsExtension extends DataExtension
{

    public static $db = array(
        'GridFieldUserColumns' => 'Text',
    );

    public function getGridFieldUserColumnsFor($gridfielddataclass)
    {
        if (!$this->owner->GridFieldUserColumns) {
            return false;
        }
        $columns = unserialize($this->owner->GridFieldUserColumns);
        return isset($columns[$gridfielddataclass]) ? $columns[$gridfielddataclass] : false;
    }

    public function setGridFieldUserColumnsFor($gridfielddataclass, $newcolumns)
    {
        $columns = $this->owner->GridFieldUserColumns ? unserialize($this->owner->GridFieldUserColumns) : array();
        $columns[$gridfielddataclass] = $newcolumns;
        $this->owner->GridFieldUserColumns = serialize($columns);
        $this->owner->write();
    }
}

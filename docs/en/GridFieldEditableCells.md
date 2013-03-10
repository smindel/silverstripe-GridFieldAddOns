# GridFieldEditableCells

GridFieldEditableCells turns your GridField into a spreadsheet. You click on a cell change the value, done. Like GridFieldExpandableForm it offers a quick way of editing a limited set of fields on a lot of records conveniently.

## Code Example

	class Foo extends DataObject {

		static $db = array('Title' => 'Varchar');

		static $many_many = array('Bars' => 'Bar');

		static $many_many_extraFields = array('Bars' => array('Relation' => 'Varchar', 'OrderBy' => 'Int'));

		function getCMSFields() {
			$fields = parent::getCMSFields();
			if($field = $fields->dataFieldByName('Bars')) {
				$extrafields = new FieldList(new TextField('Relation'));
				$field->getConfig()->addComponent(new GridFieldEditableCells($extrafields));
			}
			return $fields;
		}
	}

## Screenshots

![GridFieldEditableCells](https://raw.github.com/smindel/silverstripe-GridFieldAddOns/master/docs/en/_images/GridFieldEditableCells1.png)

*GridFieldEditableCells in action*

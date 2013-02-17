# GridFieldAddOns

## GridFieldExpandableForm

GridFieldExpandableForm is a GridField component to display a form for a GridField item like GridFieldDetailForm does, but within the GridField. It expands the item in the fashion of a jQueryUI accordion effect instead of opening the form in the main part of the UI.

By default it uses DataObject::scaffoldFormFields() to create a simple form for the selected record. Alternativly you can pass the constructor your own fieldset or form to be displyed.

GridFieldExpandableForm can be used to supply a simple form, preferrably with primitive form fields and no tabs, e.g. to enter many_many_extraFields values.

Actually you can pass the constructor any instance of ViewableData that has a loadDataFrom function.

### Code Example

	class Foo extends DataObject {

		static $db = array('Title' => 'Varchar');

		static $many_many = array('Bars' => 'Bar');

		static $many_many_extraFields = array('Bars' => array('Relation' => 'Varchar', 'OrderBy' => 'Int'));

		function getCMSFields() {
			$fields = parent::getCMSFields();
			if($this->ID) {
				$extrafields = new FieldList(new TextField('Relation'), new NumericField('OrderBy'));
				$fields->dataFieldByName('Bars')->getConfig()->addComponent(new GridFieldExpandableForm($extrafields));
			}
			return $fields;
		}
	}

## GridFieldManyManyExtraFields



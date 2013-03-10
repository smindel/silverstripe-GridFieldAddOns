# GridFieldExpandableForm

GridFieldExpandableForm is a GridField component to display a form for a GridField item like GridFieldDetailForm does, but within the GridField. It expands the item in the fashion of a jQueryUI accordion effect instead of opening the form in the main part of the UI.

To create the expandable content you have various options:

- You can pass the constructor a FieldList. The component creates the form for you including a save button and a save a routine for writing the changes to your DataObject.
- You can pass the constructor a Form instance. This option gives you full control over what gets displayed, buttons and actions and which controller handles the form after submission. The component only takes that form, loads the record and displays the form.
- Actually you can pass the constructor any instance of ViewableData that has a loadDataFrom method.
- You can implement a method called updateExandableFormFields on your DataObject, that returns a FieldList.
- You can implement a method called getExandableForm on your DataObject, that returns a form.
- You just add the component to your Gridfield. In this case GridFieldExpandableForm just calls DataObject::scaffoldFormFields() to scaffold a form for you.

GridFieldExpandableForm can be used to supply a simple form, preferrably with primitive form fields and no tabs, e.g. to enter many_many_extraFields values. It is created for GridFields displaying lots of records in situations where you have to do a lot of editing of records and where you don't want to switch between the list view and the detail form all the time.

## Code Example

	class Foo extends DataObject {

		static $db = array('Title' => 'Varchar');

		static $many_many = array('Bars' => 'Bar');

		static $many_many_extraFields = array('Bars' => array('Relation' => 'Varchar', 'OrderBy' => 'Int'));

		function getCMSFields() {
			$fields = parent::getCMSFields();
			if($field = $fields->dataFieldByName('Bars')) {
				$extrafields = new FieldList(new TextField('Relation'));
				$field->getConfig()->addComponent(new GridFieldExpandableForm($extrafields));
			}
			return $fields;
		}
	}

## Screenshots

![GridFieldExpandableForm](https://raw.github.com/smindel/silverstripe-GridFieldAddOns/master/docs/en/_images/GridFieldExpandableForm1.png)

*GridFieldExpandableForm in action*

# GridFieldUserColumns

GridFieldUserColumns gives users control over the columns of the GridField. Like for example in your mail client you can choose the columns that you want to see and change their order. This is usefull in situations when you have records with a lot of fields in your GridField and users with different information preferences. Imagine a GridField for the products of a company. Sales people would like to see the price and stock, storeman prefere location in the warehouse, whereas accounting focuses on contribution margin.

The setup of the user columns gets saved into the current member record currently. I am working on storing the infoformation in the session alternatively in case you want to avoid extending the Member class.

The component comes with two new Config classes witch add it to all instances of GridField automatically.

## Code Example

Just add the following code to the end of your mysite/_config.php

	Member::add_extension('GridFieldUserColumnsExtension');
	Object::useCustomClass('GridFieldConfig_RecordEditor', 'GridFieldConfig_ExtendedRecordEditor');
	Object::useCustomClass('GridFieldConfig_RelationEditor', 'GridFieldConfig_ExtendedRelationEditor');

## Screenshots

![Before](https://raw.github.com/smindel/silverstripe-GridFieldAddOns/master/docs/en/_images/GridFieldUserColumns1.png)

*Before*

![Dialog](https://raw.github.com/smindel/silverstripe-GridFieldAddOns/master/docs/en/_images/GridFieldUserColumns2.png)

*The Dialog*

![After](https://raw.github.com/smindel/silverstripe-GridFieldAddOns/master/docs/en/_images/GridFieldUserColumns3.png)

*After*


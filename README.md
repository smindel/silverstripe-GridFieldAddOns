# GridFieldAddOns

## Introduction

GridFieldAddOns is a collection of plugins for the Silverstripe GridField.

Currently there are 4 components:

- *[GridFieldExpandableForm](http://github.com/smindel/silverstripe-GridFieldAddOns/blob/master/docs/en/GridFieldExpandableForm.md)*
	GridFieldExpandableForm is a GridField component to display a form for a GridField item like GridFieldDetailForm does, but within the GridField. It expands the item in the fashion of a jQueryUI accordion effect instead of opening the form in the main part of the UI.
- *[GridFieldEditableCells](http://github.com/smindel/silverstripe-GridFieldAddOns/blob/master/docs/en/GridFieldEditableCells.md)*
	GridFieldEditableCells turns your GridField into a spreadsheet. You click on a cell change the value, done. Like GridFieldExpandableForm it offers a quick way of editing a limited set of fields on a lot of records conveniently.
- *[GridFieldRecordHighlighter](http://github.com/smindel/silverstripe-GridFieldAddOns/blob/master/docs/en/GridFieldRecordHighlighter.md)*
	GridFieldRecordHighlighter highlights records in a GridField.
- *[GridFieldUserColumns](http://github.com/smindel/silverstripe-GridFieldAddOns/blob/master/docs/en/GridFieldUserColumns.md)*
	GridFieldUserColumns gives users control over the columns of the GridField.

Although I am using all the components in production they are rather in a beta stage. You helping me fixing bugs or improving the module is appreciated.

## Requirements

SilverStripe Framework 3.0+

## Installation

Please follow the [standard module installation documentation](http://doc.silverstripe.org/framework/en/topics/modules). The module has to reside in a toplevel folder called `GridFieldAddOns/`.

## Maintainers

Andreas Piening <piening at 3online dot de>
Laravel-Form-Generator
======================

PHP tool to generate Laravel Blade Form/Ajax elements

It looks for MySQL databases and generates forms/ajax queries based on columns names.
There are a few naming conventions build in for the columns to effect the generation of the form elements:
 __ (double underscore)CHARACTER__ -> Capitalize character (for instance: column name = __a__pples
 _- -> Define form element type (for instance: column name = apples_-text
 _-ID -> Set column ID (for instance: column name  = apples_-ID123)
 
 Ok, to sum this up. You can have a column name like this:
 
 __a__pples_-text_-ID123 (or __a__pples_-0_-ID123 which does the same but saves space)
 
For more info on how to use these naming conventions properly see USAGE
 

<h2>Installation</h2>
Put the files inside a folder on you local machine. Open each and look for MySQL credentials inside the PDO statements. 
Make sure they match yours. Open buildform.php on your local webserver and you are good to go.
<br/><br/>
<h2>Usage</h2>

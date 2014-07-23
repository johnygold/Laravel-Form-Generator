Laravel-Form-Generator
======================

PHP tool to generate Laravel Blade Form/Ajax elements

It looks for MySQL databases and generates forms/ajax queries based on columns names.
There are a few naming conventions build in for the columns to affect the generation of the form elements:
<br/>
<ul> _ = Underscore can be used to convert to camelCase</ul>
<ul> [ID:FORMTYPE] = Columns can have ID's so they can be inserted into templates. Form type, 0=text/1=select/2=checkbox.</ul>
</li> 
 Ok, to sum this up. You can have a column name like this:
 
 user_name[1:0]
 
 
 
 Without further settings this will result into form element {{ form:text('user_name', $user_name) }}
 
 For more info on how to use these naming conventions properly see Usage
 

<h2>Installation</h2>
Put the files inside a folder on you local machine. Open each and look for MySQL credentials inside the PDO statements. 
Make sure they match yours. Open buildform.php on your local webserver and you are good to go.
<br/><br/>
<h2>Usage</h2>
The idea is that forms will be generated based on your table's column names or you set these manually. This data will first be converted to the right var naming. So you can use snake, camelCase. The tool will also look for the type of the form element and assigning ID's in case you want to use it for templating. (you can build a html template, put the columns ID's on the positions where the form elements should come, and parse it later on)

Let's say you want to have a form to edit an user's fist name. It could look something like this:

{{ Form::text('editFirstName', $user->firstName, array('class' => 'form-control', 'placeholder' => trans('placeholders.FirstName'))) }}

The tool would output the above using these settings:<br/>
<li>
<ul>column name = "first_name[X:0]" or manual mysql field input = FirstName[text]</ul>
<ul>CamelCasing ON</ul>
<ul>prefix1 = "edit", camelCasing ON</ul>
<ul>prefix2 = "$user->", camelCasing ON, lowercase OFF</ul>
<ul>class = "form-control"</ul>
</li>

So the first prefix is used to set the form name, the second, the value, and the class sets the right CSS. Note that placeholder needs a specific text that can't be devised from the columns name. Therefore I recommend using localization in Laravel, so you can define it later on inside the translation file. Same goes for labels.

If you find some bugs or inconsistencies or have some suggestions, don't hesitate to contact me. 



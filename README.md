Laravel-Form-Generator
======================

PHP tool to generate Laravel Blade Form/Ajax elements

It looks for MySQL databases and generates forms/ajax queries based on columns names.
There are a few naming conventions build in for the columns to effect the generation of the form elements:
<br/>
<ul> __ (double underscore)CHARACTER__ -> Capitalize character (for instance: column name = __a__pples</ul>
<ul> _- -> Define form element type (for instance: column name = apples_-text</ul>
<ul> _-ID -> Set column ID (for instance: column name  = apples_-ID123)</ul>
</li> 
 Ok, to sum this up. You can have a column name like this:
 
 __a__pples_-text_-ID123 (or __a__pples_-0_-ID123 which does the same but saves space)
 
For more info on how to use these naming conventions properly see USAGE
 

<h2>Installation</h2>
Put the files inside a folder on you local machine. Open each and look for MySQL credentials inside the PDO statements. 
Make sure they match yours. Open buildform.php on your local webserver and you are good to go.
<br/><br/>
<h2>Usage</h2>
The idea is that forms will be generated based on your table's column names. This include possible camelCasing for vars, defining the type of the form element but also assigning ID's in case you want to use it for templating. (you can build a html template, put the columns ID's on the positions where the form elements should come, ans parse it later on)

Let's say you want to have a form to edit an user's fist name. It could look something like this:

{{ Form::text('editfirstname', $user->firstname, array('class' => 'form-control', 'placeholder' => 'Your first name')) }}

The tool would output the above using these settings:<br/>
<li>
<ul>column name = "firstname_-text"</ul>
<ul>prefix1 = "edit"</ul>
<ul>prefix2 = "$user->"</ul>
<ul>class = "form-control"</ul>
</li>

So the first prefix is used to set the form name, the second, the value, and the class sets the right CSS. Note that placeholder gives an specific text that can't be devised from the columns name. Therefore I recommend using localization in Laravel, so you can define it later on inside the translation file. So placeholder would look like => trans('messages.firstname') Same goes for form labels.

Now, suppose you'd use camelCasing in your coding, you most likely want to have something like this:

{{ Form::text('editFirstName', $user->firstname, array('class' => 'form-control', 'placeholder' => 'Your first name')) }}

To get this result use column name = __f__irst__n__ame

If you find some bugs or inconsistencies, don't hesitate to contact me. This is just my first commit, and although usefull in some cases not perfect yet.


<?php

$dbh = new PDO('mysql:host=localhost;user=root');
$statement = $dbh->query('SHOW DATABASES');
$databaseList = $statement->fetchAll();


//Create workable array from nested data
foreach($databaseList as $key => $value)
{
	foreach($value as $key2 => $value2)
	{
		$databaseListNew[] = $value2;
	}
}

$databaseList = array_unique($databaseListNew);


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Build Laravel Form</title>
	<script language="javascript" src="http://code.jquery.com/jquery-2.1.1.js"></script>
	<script language="javascript">

		function loadTables()
		{
	        //Send AJAX call to proces further
	        var database = $("#database").val();
	        $.ajax({
	        type: "POST",
	        url: "loadtables.php",
	        dataType: "json",
	        data: { database: database }
	        })
	        .done(function(data) {
	     	var options = $("#table");
			//Remove current table options
			options.find('option').remove().end();
			//Remove current fields
			$("#fields").val('');
			//Populate again
			$.each(data, function() {
			    options.append($("<option />").val(this).text(this));
				
			});

	        }); 
		}

		function setFields()
		{
			//Send AJAX call to proces further
	        var database = $("#database").val();
	        var table = $("#table").val();
	        $.ajax({
	        type: "POST",
	        url: "loadcolumns.php",
	        dataType: "json",
	        data: { database: database, table: table }
	        })
	        .done(function(data) {
	     	var options = $("#table");

			//Remove current fields
			$("#fields").val('');
			//Populate again
			$.each(data, function() {

				//Check for snake case column names
				var posSnake = this.indexOf('_');
				var posIdType = this.indexOf('[');
				var offset = 0;
				var formType='';

				//Split column name in name and ID & FORM type
				var splitArray = this.split('[');

				if(posSnake > 0)
				{
					//Check for camelcase and adjust accordingly
					posUnderscore = splitArray[0].indexOf("_");
					substrToCamelLowerCase = splitArray[0].substr(posUnderscore, 2);
					substrToCamelUpperCase = splitArray[0].substr(posUnderscore, 2).toUpperCase();

					if($("#convertSnakeToCamel").is(":checked"))
					{
						splitArray[0] = splitArray[0].replace(substrToCamelLowerCase, substrToCamelUpperCase).replace("_","");
					}

				}
				if(posIdType > 0)
				{
					//Split column name in name and ID & FORM type
					var idTypeArray = splitArray[1].replace("[","").replace("]","").split(':');
					var id = idTypeArray[0];
					var type = idTypeArray[1];

					//Define standard type, formtype links
					if(type == 0) { formType = "text"; }
					if(type == 1) { formType = "select"; }
					if(type == 2) { formType = "checkbox"; }

				}
				if(posSnake > 0 && posIdType > 0)
				{
					$("#fields").val($("#fields").val() + splitArray[0] + "[" + formType + "],");	
				}
				else if(posSnake > 0)
				{
					$("#fields").val($("#fields").val() + splitArray[0] + "[text],");	
				}
				else if(posIdType > 0)
				{
					$("#fields").val($("#fields").val() + splitArray[0] + "[" + formType + "],");	
				}
				else
				{
					$("#fields").val($("#fields").val() + splitArray[0] + "[text],");	
				}

			});		
			});	        
		}


		function buildCode()
		{
			//Clear existing output
			clearCode();

			var fields = $("#fields").val().split(",");

			var length = fields.length-1,
			field = null;


			//Bootstrap header code
			var bootstrapHeader = "<body>\n";
			bootstrapHeader = bootstrapHeader + '    <div class="container">\n';
			var bootstrapHeader = bootstrapHeader.replace(/./g, function(e){
			return "&#"+e.charCodeAt(0)+";";
			});

			var ajax = '';
			for (var i = 0; i < length; i++) {
			  var bootstrap = '';
			  if(i != 0) { ajax = ajax + ","; }	
			  field = fields[i];
			  //Split mysql field name and type of field for HTML (text, select, checkbox etc. name[type])
			  fieldArray = field.split("[");
			  fieldName1 = fieldName2 = fieldArray[0]; //Maybe CamelCase?
			  fieldType = fieldArray[1].replace("[","").replace("]","");

			  //Check for prefix values and see if complete var names need to be converted again for proper camelCase
			  if($("#prefix1ToCamel").is(":checked"))
			  {
				fieldName1 = fieldName1.charAt(0).toUpperCase() + fieldName1.slice(1);
			  } 				  
			  if($("#prefix2ToCamel").is(":checked"))
			  {
				fieldName2 = fieldName2.charAt(0).toUpperCase() + fieldName2.slice(1);
			  }
			  else if($("#prefix2ToLowerCase").is(":checked"))
			  {
 	     		fieldName2 = fieldArray[0].toLowerCase(); //Make it lowercase
			  }

			  var code1 = $("#code1").html();
			  code1 = code1 + "{{ Form::" + fieldType + "('" + $("#prefix1").val() + fieldName1 + "', " + $("#prefix2").val() + fieldName2 + ", array('class' => '" + $("#class").val() + "', 'placeholder' =>  trans('placeholders." + fieldName1 + "')) }}<br/>";
			  $("#code1").html(code1);

			  var code2 = $("#code2").html();
			  code2 = code2 + "{{ Form::label('" + $("#prefix1").val() + fieldName1 + "', trans('labels."+ $("#prefix1").val() + fieldName1 +"')) }}<br/>";
			  code2 = code2 + "{{ Form::" + fieldType + "('" + $("#prefix1").val() + fieldName1 + "', " + $("#prefix2").val() + fieldName2 + ", array('class' => '" + $("#class").val() + "', 'placeholder' => trans('placeholders." + fieldName1 + "')) }}<br/>";
			  $("#code2").html(code2);

			  var code3 = $("#code3").html();

				bootstrap = bootstrap + '        <div class="row">\n';
				bootstrap = bootstrap + '            <div class="col-md-4">\n';
				bootstrap = bootstrap + "                <p>{{ Form::label('" + $("#prefix1").val() + fieldName1 + "', trans('labels."+ $("#prefix1").val() + fieldName1 +"')) }}</p>\n";
				bootstrap = bootstrap + '            </div>\n';
				bootstrap = bootstrap + '            <div class="col-md-8">\n';
				bootstrap = bootstrap + "                <p>{{ Form::" + fieldType + "('" + $("#prefix1").val() + fieldName1 + "', " + $("#prefix2").val() + fieldName2 + ", array('class' => '" + $("#class").val() + "', 'placeholder' => trans('placeholders." + fieldName1 + "')) }}</p>\n";
				bootstrap = bootstrap + '            </div>\n';
				bootstrap = bootstrap + '        </div>\n';


				var b = bootstrap.replace(/./g, function(e){
				return "&#"+e.charCodeAt(0)+";";
				});

				code3 = code3 + b;
				$("#code3").html(code3);
			
				var code4 = $("#code4").html();
				code4 = code4 + 'var ' + fieldName1 + ' = $("input[name=\'' + $("#prefix1").val() + fieldName1 + '\']").val();\n'; 
				ajax = ajax + fieldName1 + ": " + fieldName1;
				$("#code4").html(code4);
			}

			//Bootrap footer code
			bootstrapFooter = '    </div>\n';
			bootstrapFooter = bootstrapFooter + '</body>\n';

			var bootstrapFooter = bootstrapFooter.replace(/./g, function(e){
			return "&#"+e.charCodeAt(0)+";";
			});

			$("#code3").html("<pre>" + bootstrapHeader + $("#code3").html() + bootstrapFooter + "</pre>");


			var ajaxCall = "\n$.ajax({\n";
			ajaxCall = ajaxCall + "type: 'POST',\n";	
			ajaxCall = ajaxCall + "url: XXXXXXXXX,\n";
			ajaxCall = ajaxCall + "data: { " + ajax + "}\n";
			ajaxCall = ajaxCall + "})\n";
			ajaxCall = ajaxCall + ".done(function(data) {\n";
			ajaxCall = ajaxCall + "alert(data);\n";
			ajaxCall = ajaxCall + "});\n";


			$("#code4").html("<pre>" + code4 + ajaxCall + "</pre>");			


		}

		function clearCode()
		{
			//Clear all existing output
			$("#code1").html("");
			$("#code2").html("");
			$("#code3").html("");
			$("#code4").html("");			
		}

	</script>
</head>
<body>
<h1>LARAVEL FORM GENERATOR</h1>
<b>Usage:</b>Load fields directly from table column names, or set the Mysql fields manually.<br/>
In case you would like to generate forms directly from the mysql database, check the readme. <br/>
There is some built in flexibillity regarding var naming conventions. Set it to your liking.<br/>
For efficiency purposes this tool assumes usage of the lang file from laravel localization to set labels and placeholders.<br/>
You can set a prefix value for both the form name, and the form value elements.<br/>
Output are a few templates of standard laravel blade code, with bootstrap, and also jquery AJAX which is also a PITA when you deal with lots of form elements</br>
<br/>
Note: only form text elements give the right blade output for now, of course you can alter the output for selects, checkbox in the source.<br/><br/>
Questions / comments: ray[ at ] gmail com
<hr>
	<form action="buildform.php">
		
		<label for="database">Select database</label><br/>
		<select id="database" onChange="loadTables();">
			<?php
			foreach($databaseList as $key => $value)
			{
				echo "<option value='".$value."'>".$value."</option>";
			}
			?>
		</select><br/> 
		<label for="convertSnakeToCamel">Convert snake_case to camelCase?</label><br/>
		<input type="checkbox" id="convertSnakeToCamel"/><br/> 				
		<label for "table">Select table</label><br/>
		<select id="table" onChange="setFields();">
			
		</select><br/>
		<label for="fields">Mysql fields (name1[text],name2[select] etc.) tip: CamelCase[text]</label><br/>
		<input type="text" id="fields" size="50"/><br/>
		<label for="prefix1">Prefix name</label><br/>
		<input type="text" id="prefix1" size="20"/><label for="prefix1CamelCase">Convert to camelCase?</label><input type="checkbox" id="prefix1ToCamel"/><br/>
		<label for="prefix2">Prefix value</label><br/>
		<input type="text" id="prefix2" size="20"/><label for="prefix2CamelCase">Convert to camelCase?</label><input type="checkbox" id="prefix2ToCamel"/><label for="prefix2LowerCase">all lowercase?</label><input type="checkbox" id="prefix2ToLowerCase"/><br/>	
		<label for="class">Class</label><br/>
		<input type="text" id="class" size="20"/><br/>
		<input type="button" onClick="buildCode();" value="Generate code">&nbsp;<input type="button" onClick="clearCode();" value="Clear code">
	</form>
	<br/>
	<h2>Laravel Blade FORM elements</h2>
	<div id="code1"></div>
	<hr/>
	<h2>Laravel Blade FORM elements + LABELS (assuming localization, lang file)</h2>
	<div id="code2"></div>
	<hr/>
	<h2>Laravel Blade FORM elements + LABELS inside BOOTSTRAP (assuming localization, lang file)</h2>
	<div id="code3"></div>
	<hr/>
	<h2>AJAX</h2>
	<div id="code4"></div>


</body>
</html>
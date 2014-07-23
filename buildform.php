<?php

/* old school
$myhost="localhost";
$myuser = "";
$mypass = "root";
//$mydb = "m1_f6a179de";
$connection = mysql_connect($myhost, $myuser, $mypass);
mysql_select_db($mydb);
//mysql_set_charset('utf8');


$query = "SELECT schema_name FROM information_schema.schemata WHERE schema_name
    NOT IN ('information_schema', 'mysql', 'performance_schema')";

$result = mysqli_query($link, $query) or die(mysqli_error($link));
$dbs = array();
while($db = mysqli_fetch_row($result))
   $dbs[] = $db[0];
echo implode('<br/>', $dbs);

*/

$dbh = new PDO('mysql:host=localhost;user=root');
$statement = $dbh->query('SHOW DATABASES');
$databaseList = $statement->fetchAll();


//Maak nieuwe array uit de geneste data
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

				//Set initial code based on table conventions
				var pos = this.indexOf('__');
				var offset = 0;
				if(pos > 0)
				{
					//MySQL name conventions found, build special casing and define form element types
					var splitArray = this.split('__');

					//Check for camelcase and adjust accordingly
					amountCharacters = splitArray[0].split("_q").length - 1; 
					for(i=0;i < amountCharacters; i++)
					{
						posCamelCase1 = splitArray[0].indexOf("_q", offset);
						posCamelCase1 = posCamelCase1 + 2;
						posCamelCase2 = splitArray[0].indexOf("q_", offset);
						offset = posCamelCase2 + 2;
						var subString1 = "_q" + splitArray[0].substr(posCamelCase1, (posCamelCase2-posCamelCase1)) + "q_";
						var subString2 = subString1.toUpperCase();
						splitArray[0] = splitArray[0].replace(subString1, subString2).replace("_Q","").replace("Q_","");
					}	
					$("#fields").val($("#fields").val() + splitArray[0] + "[" + splitArray[1] + "],");	
				}
				else
				{a
					//MySQL conventions not found, so assume all = text and no special casing
					$("#fields").val($("#fields").val() + this + "[text],");	

				}

			});		
			});	        
		}


		function buildCode()
		{
			var fields = $("#fields").val().split(",");

			var length = fields.length-1,
			field = null;


			//evt header for bootstrap erbij
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
			  fieldName1 = fieldArray[0]; //Maybe CamelCase?
			  fieldName2 = fieldArray[0].toLowerCase(); //Make it lowercase
			  fieldType = fieldArray[1].replace("[","").replace("]","");

			  var code1 = $("#code1").html();
			  code1 = code1 + "{{ Form::" + fieldType + "('" + $("#prefix1").val() + fieldName1 + "', " + $("#prefix2").val() + fieldName2 + ", array('class' => '" + $("#class").val() + "', 'placeholder' => '" + fieldName1 + "')) }}<br/>";
			  $("#code1").html(code1);

			  var code2 = $("#code2").html();
			  code2 = code2 + "{{ Form::label('" + $("#prefix1").val() + fieldName1 + "', trans('messages."+ $("#prefix1").val() + fieldName1 +"')) }}<br/>";
			  code2 = code2 + "{{ Form::" + fieldType + "('" + $("#prefix1").val() + fieldName1 + "', " + $("#prefix2").val() + fieldName2 + ", array('class' => '" + $("#class").val() + "', 'placeholder' => '" + fieldName1 + "')) }}<br/>";
			  $("#code2").html(code2);

			  var code3 = $("#code3").html();

				bootstrap = bootstrap + '        <div class="row">\n';
				bootstrap = bootstrap + '            <div class="col-md-4">\n';
				bootstrap = bootstrap + "                <p>{{ Form::label('" + $("#prefix1").val() + fieldName1 + "', trans('messages."+ $("#prefix1").val() + fieldName1 +"')) }}</p>\n";
				bootstrap = bootstrap + '            </div>\n';
				bootstrap = bootstrap + '            <div class="col-md-8">\n';
				bootstrap = bootstrap + "                <p>{{ Form::" + fieldType + "('" + $("#prefix1").val() + fieldName1 + "', " + $("#prefix2").val() + fieldName2 + ", array('class' => '" + $("#class").val() + "', 'placeholder' => '" + fieldName1 + "')) }}</p>\n";
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

			//Evt bootrap footer
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

	</script>
</head>
<body>
	<form action="buildform.php">
		
		<label for="database">Select database</label><br/>
		<select id="database" onChange="loadTables();">
			<?php
			foreach($databaseList as $key => $value)
			{
				echo "<option value='".$value."'>".$value."</option>";
			}
			?>
		</select>&nbsp;Table: 
		<select id="table" onChange="setFields();">
			
		</select><br/>
		<label for="fields">Mysql fields (name1[text],name2[select] etc.) tip: CamelCase[text]</label><br/>
		<input type="text" id="fields" size="50"/><br/>
		<label for="prefix1">Prefix name</label><br/>
		<input type="text" id="prefix1" size="20"/><br/>
		<label for="prefix2">Prefix value</label><br/>
		<input type="text" id="prefix2" size="20"/><br/>	
		<label for="class">Class</label><br/>
		<input type="text" id="class" size="20"/><br/>
		<label for="placeholder"></label><br/>
		<!-- <input type="checkbox" id="placeholder"/><br/> -->				
		<input type="button" onClick="buildCode();" value="Generate code">
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
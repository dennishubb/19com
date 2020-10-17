<form method="post">

<div style="margin-bottom:15px;">
<select name="type">
<option value="mysql" <?php if(isset($_POST['type']))echo($_POST['type']=="mysql")?'selected':''; ?>>MySQL</option>
<option value="mssql" <?php if(isset($_POST['type']))echo($_POST['type']=="mssql")?'selected':''; ?>>MsSQL</option>
</select>
</div>

<div style="margin-bottom:15px;"><input type="text" name="name" placeholder="Table Name" value="<?php echo isset($_POST['name'])?$_POST['name']:''; ?>"></div>

<div style="margin-bottom:15px;"><textarea name="field" cols="50" rows="20"><?php echo isset($_POST['field'])?$_POST['field']:''; ?></textarea></div>

<div><input type="submit" name="submit" /></div>

</form>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	if(isset($_POST['type'])&&isset($_POST['name'])&&isset($_POST['field'])){

$_POST['field']=trim(preg_replace('/\s+/', ' ', $_POST['field']));
$_POST['field']=str_replace('[','',$_POST['field']);
$_POST['field']=str_replace(']','',$_POST['field']);
$_POST['field']=str_replace(' ','',$_POST['field']);
$_POST['field']=str_replace('`','',$_POST['field']);
$_POST['field']=str_replace('`','',$_POST['field']);



$fields = explode(',',$_POST['field']);


$primary_name=strtolower($fields[0]);
$primary_php_name=strtolower($fields[0]);
$primary_method_name=str_replace(' ', '', ucwords(str_replace('_',' ',$fields[0])) );

if($_POST['type']=='mssql'){
	$primary_name=$fields[0];
}


$output="
<?php

class ".$_POST['name']." {\n\n";



$output.="\tprivate $"."dbc=NULL;
";

foreach($fields as $field){

	if($_POST['type']=='mssql'){
		if(strpos($field, '_') === false){
			$array = str_split($field);
			$first=true;
			$i=0;
			$point=0;
			foreach ($array as $char) {
				if(ctype_upper($char)&&!$first){
					$field = substr_replace($field, "_", $i+$point, 0);
					$point++;
				}
				$first=false;
				$i++;
			}
		}
	}

	$name = strtolower($field);
	$output.="\tprivate $".$name."=\"\"; \n";

}

$output.="
	function __construct($"."dbc=NULL) {
		$"."this->dbc=$"."dbc;	
	}
";


foreach($fields as $field){

	if($_POST['type']=='mssql'){
		if(strpos($field, '_') === false){
			$array = str_split($field);
			$first=true;
			$i=0;
			$point=0;
			foreach ($array as $char) {
				if(ctype_upper($char)&&!$first){
					$field = substr_replace($field, "_", $i+$point, 0);
					$point++;
				}
				$first=false;
				$i++;
			}
		}
	}

	$var_name=strtolower($field);
	$method_name=str_replace(' ', '', ucwords(str_replace('_',' ',$field)) );

$output.="
    public function get".$method_name."(){
		return $"."this->".$var_name.";
	}

	public function set".$method_name."($".$var_name."){
		$"."this->".$var_name." = $".$var_name.";
	}
	";

}

//create
$output.="
	public function create(){
		
		$"."data=array(
";

$field_list=array();

$first=true;

foreach($fields as $field){

	$var_name=strtolower($field);
	$method_name=str_replace(' ', '', ucwords(str_replace('_',' ',$field)) );

	if(!$first){
		$field_list[]="\t\t\t\t\"".$field."\"=>$"."this->get".$method_name."()";
	}


	$first=false;

}

$output.=implode(",\n",$field_list);

$output.="
		);
		
		$"."id = "."$"."this->dbc->insert('".$_POST['name']."',$"."data);
		
		return $"."id;
		
	}
";

//create
$output.="
	public function load($".$primary_php_name."){
		
		$"."this->dbc->where('".$primary_name."='.$"."id".");
		$"."row = $"."this->dbc->getOne('".$_POST['name']."');
		
		if(!empty($"."row)){

";


foreach($fields as $field){

	$var_name=strtolower($field);
	$method_name=str_replace(' ', '', ucwords(str_replace('_',' ',$field)) );
	$output.="\t\t\t$"."this->set".$method_name."($"."row['".$field."']);\n";

}



$output.="            
			return true;
		}else{
			return false;
		}
		
	}
";

$output.="

	function save(){
		
		$"."data = array(
";

$field_list=array();

$first=true;

foreach($fields as $field){

	$var_name=strtolower($field);
	$method_name=str_replace(' ', '', ucwords(str_replace('_',' ',$field)) );


	if(!$first){
		$field_list[]="\t\t\t\t'".$field."'     => $"."this->get".$method_name."()";
	}


	$first=false;

}

$output.=implode(",\n",$field_list);


$output.="		
		);
 
		$"."this->dbc->where('id='.$"."this->get".$primary_method_name."()); 
		$"."this->dbc->update('".$_POST['name']."',$"."data); 
		
	}

";


$output.="
	public function remove(){
		
		$"."this->dbc->where('id='.$"."this->get".$primary_method_name."()); 
		$"."this->dbc->delete('".$_POST['name']."'); 

	}
";

$output.="

}
";

highlight_string($output);


	}else{

		echo '<div style="margin-top:10px;"></div>';

	}

}
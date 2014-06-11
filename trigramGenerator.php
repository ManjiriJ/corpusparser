<?php
$dbUser="root";
$dbHost="localhost";
$dbPsswd="";
$dbName="swc_logs_test";
$raw_table_prefix="wordfrequency";
$summary_table_prefix="wordfrequencysummary";
$lang="marathi";
global $con,$link,$dbUser,$dbHost,$dbPasswd,$dbName;

$stmt="Select distinct word,sum(frequency) f from $summary_table_prefix$lang where length(word)>=2 group by word order by f desc";
echo $stmt;
connect();

$result=mysqli_query($link,$stmt);
//$result_array=array();
$index=-1;
//echo "result: ".$result;
while( $row= mysqli_fetch_row($result) )
{	
	$index++;
	$word=$row[0];
	$freq=$row[1];
	
	$char_array=mb_str_split(trim($word));
	array_push($char_array, " ");
	//var_dump($char_array);
	echo "\ni= $index-Word: ".$word;
	
	if(count($char_array)>=2)
	{
		for($i=0;$i<(count($char_array)-2);$i++){
			echo "\t".$char_array[$i].$char_array[$i+1].$char_array[$i+2];
			 $insert="Insert into ngramfrequency(gram,n,frequency) values('".$char_array[$i].$char_array[$i+1].$char_array[$i+2]."',3,$freq) on duplicate key update frequency=frequency+".$freq;
			
			if(mysqli_query($link,$insert)!=null)
			{	
				echo "\n-- Inserted /updated successfully";
			}
			else
				echo "\n-- Insertion/updation failed :".$insert; 
		}
	}
	
}


function connect(){
global $con,$link,$dbUser,$dbHost,$dbPasswd,$dbName;
$con=mysqli_connect($dbHost,$dbUser,$dbPasswd,$dbName); //swarachakra_logging
//echo $con;
// Check connection
if (mysqli_connect_errno($con))
  {
  echo "\nFailed to connect to MySQL: " . mysqli_connect_error();
  }
  else
	echo "\nConnected";
	
	$link = mysqli_connect($dbHost,$dbUser,$dbPasswd,$dbName) or die("Error " . mysqli_error($link));
	mysqli_query($link,"SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
}

function mb_str_split( $string ) {
		# Split at all position not after the start: ^
		# and not before the end: $
		return preg_split('/(?<!^)(?!$)/u', $string );
		//return preg_split('/(?<!^)(?!$)/u', $string );
	}
	
	function mb_strcmp($str1,$str2){
		//-test- added
		//string2 is the subset of string1 and both are of same length, implies they are same
		if(mb_strlen(trim($str1))== 0 || mb_strlen(trim($str2))==0){
			echo 'One of the strings is empty:1-'.$str1.", 2-".$str2;
		}
		if(mb_stripos(trim($str1),trim($str2),0,"UTF-8")===0 && mb_strlen(trim($str1))===mb_strlen(trim($str2))){
			//echo "\n --".$str1."-- and --".$str2."-- are same";
			return true;
		}else{
			//echo "\n --".$str1."-- and --".$str2."-- arent same";	
			return  false;
		}
	
	}

?>

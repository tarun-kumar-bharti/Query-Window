<?php 

error_reporting(0);

$host        = "localhost";    
$dbUserName  = "root";
$dbPassword  = "";  //add db password here

if($_POST['dbname']!=''){
    $dbUserDatabase = $_POST['dbname'];    
    $dbname    = $_POST['dbname'];
}else{
    $dbUserDatabase = "health_test1";  ///add your default database name
    $dbname    = $_POST['dbname'];
}

$conn = mysqli_connect($host.":3306",$dbUserName,$dbPassword) or die ("Unable connect database <BR>".mysqli_error($conn));
mysqli_select_db($conn,$dbUserDatabase) or die ("Unable to select database<BR>".mysqli_error($conn));
$sqlquery = $_POST['sqlquery'];
if($sqlquery==""){    

?>

<html>
<head>
<title>Neo Query Window</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<style>
.tblstd {
	border: 1px solid black;	
	width:100%;
	text-align:center;
 } 
 
 
</style>

<body bgcolor="#FFFFFF" text="#000000">
<table cellpadding="5" cellspacing="0" align="center" width="100%" border="0">
<form name="form1" method="post" action="<?php echo $PHP_SELF;?>">
<tr>
  <td colspan="2" align="center"><img src="neologo.png"border="0"><br><br></td> 
</tr> 
 
<tr><td colspan="2" align="center"><b>Please enter the sql query whose results you want to see on the browser</b></td></tr>
<tr><td colspan="2" align="center">
<textarea name="sqlquery" rows="5" cols="70"></textarea></td>
</tr>
<tr><td colspan="2" align="center"> 
Please select the database &nbsp;
<select name="dbname">
<?php
    $res=mysqli_query($conn,"show databases") or die(mysqli_error($conn));
    while ($row=mysqli_fetch_row($res)){
        echo "<option value='$row[0]'";
        if ($row[0]=="health_test1"){echo " selected";}
        echo ">$row[0]</option>";
    }
?>
</select> 


</td>
</tr>
<tr>
	<td colspan="1" align="right" width="50%">
		<input type="submit" name="Submit" value="Submit">
	</td>
	<td align="left" width="50%">	
		<input type="button" id="showTables" value="Show Tables" onClick="showtables();">
		<input type="button" id="hideTables" value="Hide Tables" onClick="hidetables();">
    </td>
</tr>

<tr>
<td colspan="1" align="center"></td>
<td colspan="1" align="left"> 
	<table width="40%" align="left" id="tbls" class="tbls">
	<?php 
	$res1=mysqli_query($conn,"show tables") or die(mysqli_error($conn));
		while ($row1=mysqli_fetch_row($res1)){
			echo "<tr><td class='tblstd'>".$row1[0]."</td></tr>";
		}
	?>
	</table>
</td>
</tr>


</form>
</table>

<script>
document.getElementById('tbls').style.display='none';
document.getElementById('hideTables').style.display='none';
function showtables(){	 
	document.getElementById('tbls').style.display='block';
	
	document.getElementById('hideTables').style.display='block';
	document.getElementById('showTables').style.display='none';
	 
}
function hidetables(){	 
	document.getElementById('tbls').style.display='none';
	document.getElementById('hideTables').style.display='none';
	document.getElementById('showTables').style.display='block';
}

</script>


</body>
</html>
<?php exit; }else{ ?>

<html>
<head>
<title>Queries Results</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body bgcolor="#FFFFFF" text="#000000">

<?php 
     
    $store = addslashes($sqlquery); 
    global $sqlquery;    
    $sqlquery = stripslashes($sqlquery);
  
    function showerror(){
        echo "<br>Your Query - $sqlquery<BR><center><b>MySQL Error - ".mysqli_error($conn)."</b></center>";
        mysqli_close($conn);
        exit;
    } 
     
    mysqli_select_db($conn,"$dbname") or showerror();
    $arrquery=explode("\n",$sqlquery);
	 
    
	for($q=0;$q<count($arrquery);$q++){
		$sql=stripslashes($arrquery[$q]);
		$sql=trim($sql);
		$first5=strtoupper(substr($sql,0,6));

		$pos1=strpos($first5,"SELECT");
		$pos2=strpos($first5,"SHOW");
		$pos3=strpos($first5,"DESC");
		
		if (($pos1===false)&&($pos2===false)&&($pos3===false)){
			echo "<center><b>Only select queries are allowed</b><br><a href='javascript:history.go(-1)'>Click</a> here to go back</center>";
			exit;
		}
		 
		$res=mysqli_query($conn,$sqlquery) or showerror();
		$cnt=0;
		$cnt=mysqli_num_rows($res);
		echo "<center><b>Total records found :  $cnt</b><br>";
		
		if ($cnt>0){
			 $numfields=mysqli_num_fields($res);  
			 
			 
			echo "<table cellspacing='0' cellpadding='2' border='1' align='center'>";
			//echo "<tr><td>Sno</td>";
		  
			for($i=0;$i<$numfields;$i++){
				
				$fieldInfo = mysqli_fetch_field_direct($res,$i); 
			 
				$fname=$fieldInfo->name; 
				$fname=$fname;
				$ftype[$i]=$fieldInfo->type;
				 
				echo "<td align=center><b><font face=arial size=2>";		   
				echo "$fname</font></b></td>";  
			}
			
			echo "</tr>";
				
			$sno=1;
			while($row=mysqli_fetch_row($res)){
				 
				//echo "<tr><td>$sno</td>";
				
				for ($i=0;$i<$numfields;$i++){			   
					if (($ftype[$i]==3)||($ftype[$i]=="real")){						 
						echo "<td align=right><font face=arial size=2>".$row[$i]."</font></td>";					 
					}else{				
						$row[$i]=$row[$i]; 
						echo "<td><font face=arial size=2>".$row[$i]."</font></td>"; 
					}			   
				}
				 
				echo "</tr>";
				
				$sno++;    
			}
				 
			//echo "<tr><td><b><font face=arial size=2>Total</font></b></td><td><b><font face=arial size=2>$cnt</font></b></td>";
			if ($cnt>2){		
				//echo "<td colspan=($cnt-2)>&nbsp;</td>";
			}            
		 
			echo "</tr>";
			echo "</table>";
            
       }else{
            echo "<br><b>No Records</b>";
       }
   }
?>
</body>
</html>
<?php } mysqli_close($conn);?>
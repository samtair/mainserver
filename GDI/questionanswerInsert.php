<html>
    <head>
        <title>Edit Question Information</title>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="jquery-2.0.2.js"></script>
		<script src="bootstrap/js/bootstrap.js"></script>
		<script language="javascript">
		function redirectTo(url) {
                window.location = url;
				
            }
		$(document).ready(function(){
			    $("#notification").hide();
				$("#cancel").click(function(){
				redirectTo("question.php");
				
				});
				
			});
		</script>
		</head>

<?php

if(isset($_POST["qstr"]) and isset($_POST["qa1"]) and isset($_POST["qa2"]) and isset($_POST["qa3"]) and isset($_POST["qa4"]) and isset($_POST["qac"]))
{
	include 'conn.php';
	
	$query= "insert into questions(category_id,title,text) values (".$_POST["category"].",'".$_POST["qatitle"]."','".$_POST["qstr"]."');";
	
	$result = mysqli_query($connection,$query);
	if($result==FALSE)
	{
		echo("<br> problem inserting question");
		return;
	}
	else {
	$id=mysqli_insert_id($connection);
	
	//insert answer1
	$query= "insert into answers(question_id,text,correct) values (".$id.",'".$_POST["qa1"]."',";
	if($_POST["qac"]==1)
	{
		$query=$query."1);";
	}
	else
	{
		$query=$query."0);";
	}
	
	$result = mysqli_query($connection,$query);
	if($result==FALSE)
	{
		echo("<br> problem inserting answer 1");
		return;
	}
	else {
	//insert answer2
	$query= "insert into answers(question_id,text,correct) values (".$id.",'".$_POST["qa2"]."',";
	if($_POST["qac"]==2)
	{
		$query=$query."1);";
	}
	else
	{
		$query=$query."0);";
	}
	
	$result = mysqli_query($connection,$query);
	if($result==FALSE)
	{
		echo("<br> problem inserting answer 2");
		return;
	}
	else{
	//insert answer3
	$query= "insert into answers(question_id,text,correct) values (".$id.",'".$_POST["qa3"]."',";
	if($_POST["qac"]==3)
	{
		$query=$query."1);";
	}
	else
	{
		$query=$query."0);";
	}
	
	$result = mysqli_query($connection,$query);
	if($result==FALSE)
	{
		echo("<br> problem inserting answer 3");
		return;
	}
	//insert answer4
	$query= "insert into answers(question_id,text,correct) values (".$id.",'".$_POST["qa4"]."',";
	if($_POST["qac"]==4)
	{
		$query=$query."1);";
	}
	else
	{
		$query=$query."0);";
	}
	
	$result = mysqli_query($connection,$query);
	if($result==FALSE)
	{
		echo("<br> problem inserting answer 4");
		return;
	}
	else
	{Header("Location: qdetail.php?question_id=".$id);
	}
	}
}}	
}
//do stuff
?>
<body onload>
	 <br>
	     <div id="container" class="container">
		 <div class="hero-unit">
		
		<div id="notification" class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Warning!</strong><p> <p>
		</div>

<form action='questionanswerInsert.php' method='post'>
<h3>Quesiton</h3>
<b>Category:</b>
		<select id="category" name="category">
		<?php
		 include 'conn.php';
		 $result=mysqli_query($connection,
				"select * FROM categories");
		 while($row = mysqli_fetch_array($result)){
		 echo ("<option value=".$row['id'].">".$row['category']."</option>");
		}
		?>
</select><br>
Title:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='text' required id="text" name='qatitle'><br/>
Text:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<textarea required id="qstr" name='qstr'></textarea><br>
<h3>Answers</h3>

<input type='radio' name='qac' checked='true' value=1 />&nbspA)&nbsp <input id ='qa1' required name='qa1'/><br>
<input type='radio' name='qac' value=2 />&nbspB)&nbsp <input id='qa2' required name='qa2'><br>
<input type='radio' name='qac' value=3 />&nbspC)&nbsp <input id='qa3' required name='qa3'><br>
<input type='radio' name='qac' value=4 />&nbspD)&nbsp <input id='qa4' required name='qa4'><br>
<br>
<input type='submit' class='btn btn-primary' id="submit" value='Submit'>
<button type="button" id="cancel" class="btn">Cancel</button>
</form>
		</div>
		</div>
    </body>
</html>



<?php 
    $questionId = $_GET['question_id'];
?>

<html>
    <head>
        <title>Edit Question Information</title>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="jquery-2.0.2.js"></script>
		<script src="bootstrap/js/bootstrap.js"></script>
        <script language="javascript">
            /**
             * This function provides link functionality to the edit and detail
             * buttons on the main table. Redirects to a url of the user's
             * choice.
             * Parameters:
                - url(string): the url to be redirected to.
             */
            function redirectTo(url) {
                window.location = url;
				
            }
            
            //once the document has loaded.
            $(document).ready(function(){
			    $("#notification").hide(); 
                $.ajax({
                    url: "mysqlScript.php",
                    type: "GET",
                    data: {cmd:"fetch", 
                           question_id:""+<?php echo $questionId; ?>},
                    dataType: "json",
                    success: function(data, textStatus, jqXHR) {
                        var firstRow = data[0];
                        
                        $("#titleInput").val(firstRow['title']);
                        $("#qTextInput").val(firstRow['q_text']);

                        var letters = new Array("A", "B", "C", "D");
                        var CORRECT = 1;

                        for(var i = 0; i < data.length; i++) {
                            var letter = letters[i];
                            var row = data[i];

                            //string to access various components
                            var acsStr = "#ans" + letter;
                            
                            $(acsStr).val(row['a_text']);
                            
                            acsStr = "." + letter.toLowerCase() + 
                                    "Correct";
                            $(acsStr).val(row['a_id']);
                            
                            if(row['correct'] == CORRECT) {
                                $(acsStr).prop("checked", true);
                            }
                        }
                    },

                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        console.log("Error occurred! Status: " + textStatus + ". " +
                            "Type: " + errorThrown);
                    }
                });
				
                $("#cancel").click(function(){
				redirectTo("question.php");
				
				});
                $("#saveData").click(function() {
				$('#notification').show();
				 if ($("#titleInput").val()==""||$("#qTextInput").val()==""||$("#ansA").val()==""||$("#ansB").val()==""||$("#ansC").val()==""||$("#ansD").val()==""){
				 //alert('aa');
				 $('#notification').show();
				 }
				 else{
				 
                    var correctAnsId = $("input:radio[name='answer']:checked").val();
                   // alert(correctAnsId);
                    $.ajax({
                        url:"mysqlScript.php",
                        type: "POST",
                        data: {cmd:"update", 
                               question_id:<?php echo $_GET['question_id']; ?>,
                               new_title: $("#titleInput").val(),
                               new_text: $("#qTextInput").val(),
                               ans_a: $("#ansA").val(),
                               ans_b: $("#ansB").val(),
                               ans_c: $("#ansC").val(),
                               ans_d: $("#ansD").val(),
                               correct_ans_id: correctAnsId
                              },
                        success: function(data, textStatus, jqXHR) {
                           redirectTo("qdetail.php?question_id=<?php echo $_GET['question_id']; ?>");
                        },

                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR);
                            console.log("Error occurred! Status: " + textStatus + ". " +
                                "Type: " + errorThrown);
                            redirectTo("question.php");
                        }
                    });
					}
                });
            });
        </script>
    </head>
    <body onload>
	 <br>
	     <div id="container" class="container">
		 <div class="hero-unit">
		 <form>
		<div id="notification" class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Warning!</strong> Cannot be empty.
		</div>
        <label for="Title"><b>Title:</b></label>
        <input id="titleInput" type="text" required /><br />
        
        <label for="Text"><b>Question Text:</b></label>
        <textarea class="input-xxlarge" required id="qTextInput"></textarea><br />
        
        
            <label ><b>Answers:</b></label>
            <div>
                <label for="ansA">A)</label>
                <input class="input-xlarge" required id="ansA" type="text" />
                <input id="ansA" class="aCorrect" name="answer" type="radio"/>
            </div>
            
            <div>
                <label for="ansB">B)</label>
                <input class="input-xlarge" required id="ansB" type="text" />
                <input class="bCorrect" name="answer" type="radio"/>
            </div>
            
            <div>
                <label for="ansC">C)</label>
                <input class="input-xlarge" required id="ansC" type="text" />
                <input class="cCorrect" name="answer" type="radio"/>
            </div>
             
            <div>
                <label for="ansD">D)</label>
                <input class="input-xlarge"  required id="ansD" type="text" />
                <input class="dCorrect" name="answer" type="radio" />
            </div>
        

        <input class="btn btn-primary" id="saveData" type="button" value="Save" />
		<button type="button" id="cancel" class="btn">Cancel</button>
		</form>
		</div>
		</div>
    </body>
</html>

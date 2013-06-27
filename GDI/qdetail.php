<?php $questionId = $_GET['question_id']; ?>

<html>
    <head>
        <title>View Question</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="jquery-2.0.2.js"></script>
		<script src="bootstrap/js/bootstrap.js"></script>
        <script language="javascript">
            $(document).ready(function() {
                $.ajax({
                    url: "mysqlScript.php",
                    type: "GET",
                    data: {cmd: "fetch", 
                            question_id: "" + <?php echo $questionId; ?>},
                    dataType: "json",
                    success: function(data, textStatus, jqXHR) {
                        var firstRow = data[0];
                        var letters = new Array("A", "B", "C", "D");

                        $("#titleText").prop("innerHTML", firstRow['title']);
                        $("#category").prop("innerHTML", firstRow['category']);
                        $("#qText").prop("innerHTML", 
                            firstRow['q_text']);

                        var answers = "", CORRECT = 1;
                        for(var i = 0; i < data.length; i++) {
                            var row = data[i];
                            
                            answers += letters[i] + ") " + row['a_text'];
                            
                            if(row['correct'] == CORRECT) {
                                answers += "<p style='color: red; " +
                                    "display: inline; margin-left: 1em;'>" +
                                    "<b class='label label-important'>CORRECT ANSWER</b></p>";
                            }
                            
                            answers += "<br />";
                        }
                        
                        $("#answerList").prop("innerHTML", answers);
                    }
                });
                $("#golist").click(function() {
				redirectTo("question.php");
				});
				
				
                $("#deleteButton").click(function() {
				    $('#myModal').modal('show');
				    $('.modal-body p').text('Do you really want to delete?');
					$('#del').click(function(){
                    $.ajax({
                        url: "mysqlScript.php",
                        type: "GET",
                        data: {cmd: "delete", 
                               question_id: "" + <?php echo $questionId; ?>},

                        success: function(data, textStatus, jqXHR) {
                            redirectTo("question.php");
                            //alert("Question deleted successfully!");
                        },

                        error: function(jqXHR, textStatus, errorThrown) {
                            redirectTo("question.php");
                            console.log("Deletion request failed! Error: " 
                                + errorThrown);
                            alert("Deletion request failed! Error: " 
                                + errorThrown);
                        }
                    });
					$('#myModal').hide();
					});
                });
            });

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
			function gopage(next) {
			    $('#del').hide();
			    if(next==0) {
				<?php 
				include 'conn.php';
				$result=mysqli_query($connection,
				"select id FROM questions WHERE id<$questionId order by id desc limit 1");
					
					$row = mysqli_fetch_array($result);
				?>
			    var qid= '<?php echo $row['id']; ?>';
				if (qid==""||qid==null)
				{$('#myModal').modal('show');
				 $('.modal-body p').text('This is already the first question.');}
				else
				redirectTo("qdetail.php?question_id=<?php echo $row['id']; ?>");
				}
				else {
				<?php 
				include 'conn.php';
				$result=mysqli_query($connection,
				"select id FROM questions WHERE id>$questionId order by id asc limit 1");
				$row = mysqli_fetch_array($result);
						
				?>
				var qid='<?php echo $row['id']; ?>';
				if (qid==""||qid==null)
				{$('#myModal').modal('show');
				$('.modal-body p').text('This is already the last question.'); }
				else
				redirectTo("qdetail.php?question_id=<?php echo $row['id']; ?>");
				}
				
				
				
			}
			
        </script>
    </head>
    
    <body>
	    <br>
	     <div id="container" class="container">
		 <div class="hero-unit">
		<small><p class="muted text-left" style="display: inline;">Category:</p>
        <p class="muted " style="display: inline;" id="category"></p></small><br><br>
		<p style="display: inline;"><strong>Title:</strong></p>
        <p style="display: inline;" id="titleText"></p>
        <p><strong>Question:</strong></p>
        <p id="qText" width="600px"></p>
        <p><strong>Answers:</strong></p>
        <p id="answerList" style="display: inline;"  width="600px"></p>
		<br>
        <input class="btn btn-danger" id="deleteButton" type="button" onclick="onDeleteClick();" value="Delete" />
          <input class="btn btn-warning"
            id="qedit.php?question_id=<?php echo $questionId; ?>"
            type="button" value="Edit" onclick="redirectTo(this.id);">
        </input>
		 <input class="btn btn-success" onclick="redirectTo('questionanswerInsert.php')" type="button"  value="Add new question" />
		 <input class="btn btn-info" id="golist" type="button"  value="Go Back to List" />
        <ul class="pager">
		  <li><a href="javascript:gopage(0)" ><<</a></li>
		 
		  <li><a href="javascript:gopage(1)">>></a></li>
		</ul>
		
		
        <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
			<h3 id="myModalLabel">Warning</h3>
		  </div>
		  <div class="modal-body">
			<p>Do you really want to delete this question?
			Deleted data cannot recover.</P>
		  </div>
		  <div class="modal-footer">
			<button id='cancel' class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
			<button id='del' data-dismiss="modal" aria-hidden="true" class="btn btn-primary">Delete</button>
		  </div>
		</div>
		</div>
		</div>
    </body>
</html>

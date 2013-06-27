
<html>
    <head>
        <title>View Questions</title>
		<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
        <script type="text/javascript" src="jquery-2.0.2.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
        <script language="javascript">          
            /**
             * This function create an instance of an object that helps file
             * XMLHTTP requests from the server. This function is mostly meant
             * to factor out code that was common to the functions below.
             * 
             * @return an object that can make XMLHTTP requests with the server.
             */
             function createXMLHttpRequest() {
                return window.XMLHttpRequest ? new XMLHttpRequest() :
                    new ActiveXObject("Microsoft.XMLHTTP");
             }
            
            /**
            * This function is called during the loading of the web page
            * to initialise the drop down menu containing all categories
            * in the database.
            */
            function loadCategoryList() {
                var xmlhttp = createXMLHttpRequest();
                        
                xmlhttp.onreadystatechange = function() {
                    var OK = 4, COMPLETE = 200;
                                
                    if(xmlhttp.readyState == OK && 
                        xmlhttp.status == COMPLETE) {
                        //populate the list when the request has been completed
                        document.getElementById("categoryList").innerHTML 
                            = xmlhttp.responseText;
                    }
                }
                        
                xmlhttp.open("GET", 
                    "mysqlScript.php?cmd=load_categories", true);
                xmlhttp.send();
            }
                
            /**
             * This function is responsible for both loading the data table
             * at the initial loading of the web page and loading a new table
             * when the user selects a new category.
             *
             * @param category the category from which a questions data table
             * will be loaded. If nothing is passed, default value is null.
             */
            function loadDataTable(category,page) {
                if(typeof(category) === 'undefined') {
                    category = null;
                }
				//alert(category);
                if(typeof(page) === 'undefined') {
                    page = 1;
                }     
                var xmlhttp = createXMLHttpRequest();

                xmlhttp.onreadystatechange = function() {
                    var OK = 4, COMPLETE = 200;
                                
                    if(xmlhttp.readyState == OK && 
                        xmlhttp.status == COMPLETE) {
                        //populate the table when the request has been completed
                        document.getElementById("dataTable").innerHTML = 
                            xmlhttp.responseText;
                    }
                }
                
                xmlhttp.open("GET", 
                    "mysqlScript.php?cmd=load_table&category=" +
                    category +"&page="+page+ "&t=" + (new Date()).valueOf(), true);
                xmlhttp.send();
            }

            /**
             * This function is called by the body of the webpage when it has
             * finished loading. This function is responsible for populating 
             * the initial data table from the MySQL database on the server
             * so it can be viewed and modified. Effectively, this function 
             * calls a PHP script to populate the data table and then displays
             * it. 
             */
            function onPageLoad() {
                loadCategoryList();
                loadDataTable();
            }

            /**
             * This function is called by each of the buttons in the leftmost
             * column of the main table displaying questions.
             * This function is responsible for deleting the row containing
             * the button that was clicked from both the table and the
             * underlying MySQL database.
             *
             * @param rowNumber a number representing the index containing the 
             * row to be deleted
             */
            function deleteRowFromTable(rowNumber) {
			   $('#myModal').modal('show');
			   $('#del').click(function(){
                var table = document.getElementById("dataTable");
                var rows = table.rows;                                  
                var questionId = rows[rowNumber].id;
                            
                table.deleteRow(rowNumber);
                            
                for(var i = rowNumber; i < rows.length; i++) {
                    rows[i].cells[0].childNodes[0].id--;
                }

                $.ajax({
                    url: "mysqlScript.php",
                    type: "GET",
                    data: {cmd: "delete", question_id: questionId}
                });
				$('#myModal').hide();
				});
			   $('#cancel').click(function(){
			   
			   
			   });
            }

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
			 function search(){
			 var sertxt=document.getElementById('stxt').value;
			 var category=document.getElementById('categoryList').value;
			// alert (category);
			 var xmlhttp = createXMLHttpRequest();
              xmlhttp.onreadystatechange = function() {
                    var OK = 4, COMPLETE = 200;
                                
                    if(xmlhttp.readyState == OK && 
                        xmlhttp.status == COMPLETE) {
                        //populate the table when the request has been completed
                        document.getElementById("dataTable").innerHTML = 
                            xmlhttp.responseText;
                    }
                }                  
                    xmlhttp.open("GET","mysqlScript.php?cmd=search&category=" + category +"&s="+sertxt,  true);
                    xmlhttp.send();
			document.getElementById('stxt').value="";
			 
			 }
        </script>
    </head>
	
    <body onload="onPageLoad();" >
	<br>
      <div id="container" style="width:90%" class="container">
		 <div class="hero-unit"> 
        <input type='button' class='center btn btn-success' onclick="redirectTo('questionanswerInsert.php')" value='Add new Question'/>
		<p></p><b>Select category:</b>
        <select id="categoryList" 
            onchange="loadDataTable(this.options[this.selectedIndex].value);">
        </select>
        <div class="input-append">
        <input id='stxt' type="text" class="span3">
        <button type="button" class='btn btn-primary' onclick='search()'>Search</button>
		</div>
        
		<p></p>
        <table class='table table-hover'id='dataTable' width='1024' border='1'></table>
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
			<h3 id="myModalLabel">Warning</h3>
		  </div>
		  <div class="modal-body">
			<p>Do you really want to delete this question?</p>
			<p>Deleted data cannot recover.</P>
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

<?php
	/* Author: Curtis Cali
	 * Last Modified: June 12, 2013
	 * This file can be used by any file that seeks to interface with the
     * Krowdbyz database.
     *
     * The commands that this file recognises and their URL parameters are as 
     * follows:
     *   - delete (This command will delete a given question_id from the FB)
     *     - question_id(parameter type int): the id to be removed
     *   - fetch (This command retrieves category, title, text, and answer info
     *   for a given question_id from the DB)
     *     - question_id(parameter type int): the id to gather the info of.
     *   - load_table (This command will create HTML table data containing
     *   information for all questions in a category.
     *     - category(parameter type string): the category to gather questions
     *     from.
     *   - load_categories (this command creates HTML data for a drop down menu
     *   containing all categories in the Krowdbyz DB.
	 */

	/* Change the four below variables based on server 
	 * information
	 */ 	 
	
	
	include 'conn.php';

	$queryResult = null;
	
	/**
     * This function populates the list of categories so the user can select
     * the category of questions he/she wants to view
     */
	function populateCategoryList($resultOfQuery) {
		$list = "<option value='All'>All</option>";
		
		while($row = mysqli_fetch_array($resultOfQuery)) {
			$category = $row['category'];
			
			$list .= "<option value='$category'>$category</option>";
		}
		
		return $list;
	}
	
    /**
     * This function accesses a MySQL query containing information about
     * questions and inserts the formatted information into a data table for
     * viewing by the user
     */
	function populateDataTable($connection,$resultOfQuery) {
		$table = "
				  <th>Title</th>
				  <th>Category</th>
				  <th>Text</th>
				  <th>Operation</th>";
				  
	    $rownum = 1; /* a variable that assigns indexes to buttons through
					  * their IDs, which are then used for row deletion */
		
		
		while($row = mysqli_fetch_array($resultOfQuery)) {
			$questionId = $row['id'];
			
			$questionTitle = $row['title'];
			$category = $row['category'];
			$questionText = $row['text'];
		
			$table .= "<tr id=$questionId>";

			
					
			$table .= "<td>" . $questionTitle . "</td>";
			$table .= "<td>" . $category . "</td>";
			$table .= "<td>" . $questionText . "</td>";
			$table .= "<td class='span2'><div class='btn-group'>
			            <a id=$rownum 
						value='Delete' title='delete' class='btn btn-danger'
						onclick='deleteRowFromTable(this.id)'><i class='icon-white icon-remove'></i></a>";
			
            $table .= "<a id='qedit.php?question_id=$questionId'
						 value='Edit' class='btn btn-warning' title='edit'
						onclick='redirectTo(this.id)'><i class='icon-white icon-edit'></i></a>";
			
            $table .= "<a id='qdetail.php?question_id=$questionId'
						 value='View Details' title='view details' class='btn btn-info'
						onclick='redirectTo(this.id)'><i class=' icon-eye-open icon-white'></i></a></td>";
			$table .= "</div></tr>";
			
			$rownum++;
		}
        
		//return the populated table as HTML text
		return $table;
	}
	
	if(mysqli_connect_errno($connection)) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
	} else{
		$requestResult = "";
		$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : $_POST['cmd'];
		switch($cmd) {
			case "delete": {
				$questionId = $_GET['question_id'];              
                
				mysqli_query($connection,
					"DELETE FROM `answers` WHERE `question_id`=$questionId");
				mysqli_query($connection,
					"DELETE FROM `questions` WHERE `id`=$questionId");
				
				$requestResult = "Deletion Successful";
				break;
			}
            
            case "fetch": {
                $questionId = $_GET['question_id'];
    
                $queryResult = mysqli_query($connection, 
                    "SELECT `categories`.category, `questions`.title, 
                    `questions`.text AS q_text, `answers`.text AS a_text, 
                    `answers`.correct, `answers`.id AS a_id FROM `questions`, 
                    `answers`, `categories` WHERE `questions`.id=$questionId 
                    AND `answers`.question_id=`questions`.id AND 
                    `questions`.category_id=`categories`.id 
										
			");
                    
                $jsonResults = array();
                
                while($row = mysqli_fetch_array($queryResult)) {
                    $rowAssoc = array(
                        'category' => $row['category'],
                        'title' => $row['title'],
                        'q_text' => $row['q_text'],
						'a_id' => $row['a_id'],
                        'a_text' => $row['a_text'],
                        'correct' => $row['correct']
                    );
                    
                    array_push($jsonResults, $rowAssoc);
                }
                
                $requestResult = json_encode($jsonResults);
                break;
				}
			
			case "load_table": {
				$category = $_GET['category'] === 'null' ? 'All' : 
					$_GET['category'];
				$Page_size=15; 
				if(!isset($category) || $category === 'All') { 
					$queryResult = mysqli_query($connection, 
                        "SELECT * from questions");
				} else {
                    $queryResult = mysqli_query($connection, 
                        "SELECT `categories`.category, `questions`.id, 
                        left( `questions`.text,50) as text, `questions`.title FROM `questions`, 
                        `categories` WHERE `categories`.category='$category' AND
                        `questions`.category_id=`categories`.id");
                }
				$count = mysqli_num_rows($queryResult); 
				$page_count = ceil($count/$Page_size);
				if ($page_count==0)$page_count=1;
				$init=1; 
				$page_len=7; 
				$max_p=$page_count; 
				$pages=$page_count; 
				if(empty($_GET['page'])||$_GET['page']<0){ 
				$page=1; 
				}else { 
				$page=$_GET['page']; 
				} 
				$offset=$Page_size*($page-1); 
				//nothing chosen, so by default first category will be chosen.
				if(!isset($category) || $category === 'All') { 
					$queryResult = mysqli_query($connection, 
                        "SELECT `categories`.category, `questions`.id, 
                        left( `questions`.text,50) as text, `questions`.title FROM `questions`, 
                        `categories` WHERE `questions`.category_id=`categories`.id limit $offset,$Page_size");
				} else {
                    $queryResult = mysqli_query($connection, 
                        "SELECT `categories`.category, `questions`.id, 
                        left( `questions`.text,50) as text, `questions`.title FROM `questions`, 
                        `categories` WHERE `categories`.category='$category' AND
                        `questions`.category_id=`categories`.id limit $offset,$Page_size");
                }
                
		
                $requestResult = populateDataTable($connection,$queryResult);
				$page_len = ($page_len%2)?$page_len:$pagelen+1; 
				$pageoffset = ($page_len-1)/2;

				$key="<div class='pagination'>"; 
				//$key.="<span>$page/$pages</span> ";  
				if($page!=1){ 

				$key.="<ul><li><a onclick=\"loadDataTable('".$category."',".($page-1).")\">Prev</a></li>";  
				}else { 

				$key.="<ul><li class='disabled'><a>Prev</a></li>";
				} 
				if($pages>$page_len){ 

				if($page<=$pageoffset){ 
				$init=1; 
				$max_p = $page_len; 
				}else{
				 
				if($page+$pageoffset>=$pages+1){ 
				$init = $pages-$page_len+1; 
				}else{ 

				$init = $page-$pageoffset; 
				$max_p = $page+$pageoffset; 
				} 
				} 
				} 
				for($i=$init;$i<=$max_p;$i++){ 
				if($i==$page){ 
				$key.=" <li class='disabled'><a>".$i."</a></li>"; 
				} else { 
				$key.=" <li><a style='color:blue' onclick=\"loadDataTable('".$category."',".$i.")\">".$i."</a></li>"; 
				} 
				} 
				if($page!=$pages){ 
				$key.=" <li><a style='color:blue' onclick=\"loadDataTable('".$category."',".($page+1).")\">Next</a> </li></ul>";

				}else { 
				$key.="<li class='disabled'><a>Next</a></li></ul>";

				} 
				$key.='</div>'; 

				 
				$requestResult.="<tr> <td colspan='6'><center>". $key ."</center></td></tr> ";		
								
								
								
								
				
				break;
			}
			
			case "load_categories": {
				$queryResult = mysqli_query($connection, 
					"SELECT `category` FROM categories");
					
				$requestResult = populateCategoryList($queryResult);
				break;
			}
			
			case "search":{
			$category=$_GET['category'];
			$stxt=$_GET['s'];
			if ($category=='All'){
			$queryResult=mysqli_query($connection, "SELECT `categories`.category, `questions`.id, 
                        left( `questions`.text,50) as text, `questions`.title FROM `questions`, 
                        `categories` WHERE `questions`.category_id=`categories`.id AND (text like '%$stxt%' or title like '%$stxt%')");
						}
			else
			$queryResult = mysqli_query($connection, 
					"SELECT `categories`.category, `questions`.id, 
                        left( `questions`.text,50) as text, `questions`.title FROM `questions`, 
                        `categories` WHERE `categories`.category='$category' AND
                        `questions`.category_id=`categories`.id AND (text like '%$stxt%' or title like '%$stxt%')");
			 $requestResult = populateDataTable($connection,$queryResult);
			break;
			}
			case "update": {
                $questionId = $_POST['question_id'];
                $newTitle = $_POST['new_title'];
                $newText = $_POST['new_text'];
                $correctId = $_POST['correct_ans_id'];
                $newAnswers = array($_POST['ans_a'], $_POST['ans_b'], 
                    $_POST['ans_c'], $_POST['ans_d']);

                mysqli_query($connection, "UPDATE `questions` 
                    SET title='$newTitle', text='$newText' WHERE id=$questionId");

                //update all answers
                $queryResult = mysqli_query($connection, "SELECT id FROM 
                    `answers` WHERE question_id=$questionId");

                $ansIndex = 0;
                while($row = mysqli_fetch_array($queryResult)) {
                    $correct = null;
                    $ansId = $row['id'];
                    $answer = $newAnswers[$ansIndex];

                    $correct = $ansId == $correctId ? 1 : 0;

                    mysqli_query($connection, "UPDATE `answers` 
                        SET text='$answer', correct=$correct WHERE 
                        id=$ansId");

                    $ansIndex++;
                }

                break;
            }
		}
		
		echo $requestResult;
	}
?>

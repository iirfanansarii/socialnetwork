<?php

//$con = mysql_connect("localhost","root","") or die ("Connection was not established");

$con = mysqli_connect("localhost","root","","social_media");


//functions for getting post 

    function getTopics(){       
	global $con;           
	$get_topics = "select * from topics";
	$run_topics = mysqli_query($con,$get_topics);
    while($row = mysqli_fetch_array( $run_topics)){
    $topic_id = $row['topic_id'];
    $topic_name = $row['topic_name'];
	echo "<option value= '$topic_id '>$topic_name</option>";
	   }
	   
   }


// functoin for inserting post

  function insertPost(){
	  
	  if(isset($_POST['sub'])){
		  
		  global $con;
		  global $user_id;
		  $title   = addslashes ($_POST['title']);
		  $content = addslashes ($_POST['content']);
		  $topic = $_POST['topic'];
		  
		  if($content=='' OR $title==''){
		  echo "<h2>Please enter topic description</h2>";
			  
			  
		exit();
		  }
		  
           else {

        $insert = "insert into posts(user_id,topic_id,post_title,post_content,post_date)
		values('$user_id','$topic','$title','$content',NOW())";
		
		//NOW is use to insert user date
		
		
		$run = mysqli_query($con,$insert);

               if($run)  {

				   
	    echo "<h3>Posted to timeline,Looks great!</h3>";	


              $update = "update users set posts='yes' where user_id='$user_id'";
			  $run_update = mysqli_query($con,$update);  
				}		
 		    }			   
		  }
		  
	  }
	  
	  //function for displayin post
	  
	  
	function get_post(){
      
	  global $con;
	  
	  
	  //pagination logic starts here
	  
	  $per_page=5;
	  
	  if(isset($_GET['page'])){
	  $page = $_GET['page'];
		  
       }
	 
	 else {
		 
	 $page = 1;
	
	}
	 
	 $start_from = ($page-1) * $per_page;
	 
	 // below login for new post come first i.e descending order
	 
	 $get_posts  = "select * from posts ORDER by 1 DESC LIMIT $start_from,$per_page";

     $run_posts  = mysqli_query($con,$get_posts);
	 
	 while($row_posts = mysqli_fetch_array($run_posts)){
		 
		 $post_id     = $row_posts['posts_id'];
		 $user_id     = $row_posts['user_id'];
		 $post_title  = $row_posts['post_title'];
		 
		 // below using substr to display only 150 character in substract 
		 
		 $content     = substr($row_posts['post_content'],0,150);
		 $post_date   = $row_posts['post_date'];
		 
		 
		 //pagination logic ends here
		 
		 //getting the user who has posted i.e user name and post
		 
	$user = "select * from users where user_id='$user_id' AND posts='yes'";	 
    
    $run_user = mysqli_query($con,$user);
    $row_user = mysqli_fetch_array($run_user);
	$user_name = $row_user['user_name'];
    $user_image = $row_user['user_image'];
	

  //displaying all at once 
		
  echo "<div id='posts'>
		
	<p ><img src='users/$user_image' width='50' height='50' ></p>
	<h3><a href='user_profile.php?u_id= $user_id'>$user_name</a></h3>	
	<h3>$post_title</h3>
    <P>$post_date</p>
    <p>$content</p>	
	<p id='reply'><a href='single.php?post_id=$post_id' style='float:right;'> Reply</a></p>

   </div><br/>
   ";
	 }
	  include("pagination.php");
	 
	}		
	  
 // single page start here
 
    function single_post(){
		
  if(isset($_GET['post_id'])){
			
  global $con;
		
  $get_id  = $_GET['post_id'];
  
  $get_posts = "select * from posts where posts_id='$get_id'";
  
  $run_posts = mysqli_query($con,$get_posts);
  
  $row_posts = mysqli_fetch_array($run_posts);
  
             $post_id    = $row_posts['posts_id'];
			 $user_id    = $row_posts['user_id'];
			 $post_title = $row_posts['post_title'];
			 $content    = $row_posts['post_content'];
			 $post_date  = $row_posts['post_date'];
			 
  
 //getting the user who has posted the thread
 
 $user = "select * from users where user_id='$user_id' AND posts='yes'";
		$run_user   = mysqli_query($con,$user);
		  $row_user   = mysqli_fetch_array($run_user);
		  $user_name  = $row_user['user_name'];//--['---no space--user_name---no space--']
		  $user_image = $row_user['user_image'];
		
// getting the user session	who is commenting i.e com

   $user_com = $_SESSION['user_email'];
   $get_com  = "select * from users where user_email='$user_com'";
   $run_com  = mysqli_query($con,$get_com);
   $row_com  = mysqli_fetch_array($run_com);
   $user_com_name = $row_com['user_name'];  
	
//now displaying all at once

    		
  echo "<div id='posts'>
		
	<p ><img src='users/$user_image' width='50' height='50' ></p>
	<h3><a href='user_profile.php?u_id= $user_id'>$user_name</a></h3>	
	<h3>$post_title</h3>
    <P>$post_date</p>
    <p>$content</p>	
	<p id='reply'><a href='single.php?post_id=$post_id' style='float:right;'> Reply</a></p>

   </div>
   ";
		
		
		//logic for comment
		
	include("comments.php");
 
     echo "
	   <form action='functions/confirm_reply.php?post_id=$post_id && user_id=$user_id && user_com_name=$user_com_name' method='post' id='reply'>
	     <textarea cols='50' rows='5' name='comment' id='comment' placeholder='write your reply' required='required'></textarea><br>
	     <input type='submit' name='reply' value='Reply to This' />
	  </form>
	 
	 "; 
	 
	 //method to insert comment
	// THIS CODE IS SEPERATED ON ANOTHER PAGE THAT IS confirm_reply.php page
	
	
	}
		
	 }
	 // single page stop here
	 
	 
	 
	 
	 
	 //see all memebers logic start here
	 
	 function members(){
	  	 
		 global $con;
		 
		 //select new members
		 
		 $user = "select * from users";           // LIMIT 0,20";
		 
		 $run_user = mysqli_query($con,$user);
		 
        while($row_user=mysqli_fetch_array($run_user)){
           
		   $user_id    =  $row_user['user_id'];
		   $user_name  =  $row_user['user_name'];
		   $user_image =  $row_user['user_image'];
		   
		   
		   echo "
		   
		     <span>
			      <a href='user_profile.php?u_id=$user_id'>
			   <img src='users/$user_image' width='80' height='80' title='$user_name' style='float:left; margin:10px; border-radius:10px' />
			   </a>
            </span>
		  
		   ";

		}			
		 
	 }
	 
	//---see all memebers logic ends here------//
		
		
		
		
		
	// functon to display my posts start here 
	
		function user_posts(){
			
			 global $con;
			 
			if(isset($_GET['u_id'])) {
			$u_id = $_GET['u_id'];
			}
			
		$get_posts = "select * from posts where user_id='$u_id' ORDER by 1 DESC LIMIT 5";
		$run_posts  = mysqli_query($con,$get_posts);
			
		while($row_posts = mysqli_fetch_array($run_posts)){

		  $post_id    = $row_posts['posts_id'];
		  $user_id    = $row_posts['user_id'];
		  $post_title = $row_posts['post_title'];
		  $content    = $row_posts['post_content'];
		  $post_date  = $row_posts['post_date'];
		  
		  //getting the user who was posted the thread
		  $user = "select * from users where user_id='$user_id' AND posts='yes'";
		       
			 $run_user  = mysqli_query($con,$user);
			 $row_user  = mysqli_fetch_array($run_user);
			 $user_name  = $row_user['user_name'];
			 $user_image  = $row_user['user_image'];
			 
		
		
		//now displaying all at once
		 
		 echo "<div id='posts'>
		 
		   <p><img src='users/$user_image' with='50' height='50'</p>
		   <h3><a href='user_profile.php?user_id=$user_id'>$user_name</a></h3>
           <h3>$post_title</h3>
		   <p>$post_date</p>
		   <p>$content</p>
		   
		   
		   <a href='single.php?post_id=$post_id'
		   style='float:right;'><button>View</button></a>
		   
		    <a href='edit_post.php?post_id=$post_id'
		   style='float:right;'><button>Edit</button></a>
		   
		   
		    <a href='functions/delete_post.php?post_id=$post_id'
		   style='float:right;'><button>Delete</button></a>
		   
		   
		   
		 
		 </div><br>
		 
		 ";
		
		
   include ("delete_post.php");	
   
		     }			
			
		}
		
 
  //functon to display my posts ends here	 
   

   //function to diplay user profile starts here
      
       function user_profile(){
          
		  if(isset($_GET['u_id'])){
			  
			  global $con;
			  
		$user_id  = $_GET['u_id'];
         
		$select = "select * from users where user_id='$user_id'"; 
 		$run= mysqli_query($con,$select);
		$row  = mysqli_fetch_array($run);
		
		$id            = $row['user_id'];
		$image         = $row['user_image'];
		$name          = $row['user_name'];
		$country       = $row['user_country'];
		$gender        = $row['user_gender'];
		$last_login    = $row['user_last_login'];
		$register_date = $row['user_reg_date'];
		
		
		if($gender=='male'){
			$msg= "Send him a message";
			
		}
		else{
      
	     $msg="Send her a message"; 
	  
		} 			
           

         echo "<div id='user_profile'>
		    
			<img src='users/$image' width='80' height='80' /><br/>
		 
	        <p><strong>Name:</strong>$name</p><br>
            <p><strong>Gender:</strong>$gender</p><br>	
			<p><strong>Country:</strong>$country</p><br>	
			<p><strong>Last Login:</strong>$last_login</p><br>
			<p><strong>Member Since:</strong>$register_date</p><br>

			<a href='messages.php?u_id=$id'><button>$msg</button></a><hr>
			
		 
		   </div> 
		";		   
		  }
   
   
      } 
   
   //function to display user profie ends here
   
   
   
   
   //function for topic wise display starts here
   
   function show_topics(){
			
			 global $con;
			 
			if(isset($_GET['topic'])) {
			$id = $_GET['topic'];
			}
			
		$get_posts = "select * from posts where topic_id='$id' ";
		
		$run_posts  = mysqli_query($con,$get_posts);
			
		while($row_posts = mysqli_fetch_array($run_posts)){

		  $post_id    = $row_posts['posts_id'];
		  $user_id    = $row_posts['user_id'];
		  $post_title = $row_posts['post_title'];
		  $content    = $row_posts['post_content'];
		  $post_date  = $row_posts['post_date'];
		  
		  //getting the user who was posted the thread
		  $user = "select * from users where user_id='$user_id' AND posts='yes'";
		       
			 $run_user  = mysqli_query($con,$user);
			 $row_user  = mysqli_fetch_array($run_user);
			 $user_name  = $row_user['user_name'];
			 $user_image  = $row_user['user_image'];
			 
		
		
		//now displaying all at once
		 
		 echo "<div id='posts'>
		 
		   <p><img src='users/$user_image' with='50' height='50'</p>
		   <h3><a href='user_profile.php?user_id=$user_id'>$user_name</a></h3>
           <h3>$post_title</h3>
		   <p>$post_date</p>
		   <p>$content</p>
		   
		   
		   <a href='single.php?post_id=$post_id'
		   style='float:right;'><button>View</button></a>
		   
		    <a href='edit_post.php?post_id=$post_id'
		   style='float:right;'><button>Edit</button></a>
		   
		   
		    <a href='functions/delete_post.php?post_id=$post_id'
		   style='float:right;'><button>Delete</button></a>
		   
		   
		   
		 
		 </div><br>
		 
		 ";
		
		
   include ("delete_post.php");	
   
		     }			
			
		}
		
   //function for topic wise display ends here
   
   
   
   //functon for searching category post starts here
   
     function results(){ 
     
	global $con;
	
    if(isset($_GET['search'])){	  
	   
    $search_query = $_GET['user_query'];
	
	}
	   
	$get_posts = "select * from posts where post_title like '%$search_query%' OR post_content like '%$search_query%'";
	   
	$run_posts= mysqli_query($con,$get_posts);
	   
	while($row_posts = mysqli_fetch_array($run_posts)){
		   
      $post_id    = $row_posts['posts_id'];
	  $user_id    = $row_posts['user_id'];
	  $post_title = $row_posts['post_title'];
	  $content    = $row_posts['post_content'];
	  $post_date  = $row_posts['post_date'];
	 
	 
	 //getting the user who was posted the thread
		  $user = "select * from users where user_id='$user_id' AND posts='yes'";
		       
			 $run_user  = mysqli_query($con,$user);
			 $row_user  = mysqli_fetch_array($run_user);
			 $user_name  = $row_user['user_name'];
			 $user_image  = $row_user['user_image'];
			 
		
		
		//now displaying all at once
		 
		 echo "<div id='posts'>
		 
		   <p><img src='users/$user_image' with='50' height='50'</p>
		   <h3><a href='user_profile.php?user_id=$user_id'>$user_name</a></h3>
           <h3>$post_title</h3>
		   <p>$post_date</p>
		   <p>$content</p>
		   
		   
		   <a href='single.php?post_id=$post_id'
		   style='float:right;'><button>View</button></a>
		   
		    <a href='edit_post.php?post_id=$post_id'
		   style='float:right;'><button>Edit</button></a>
		   
		   
		    <a href='functions/delete_post.php?post_id=$post_id'
		   style='float:right;'><button>Delete</button></a>
		   
		   
		   
		 
		 </div><br>
		 
		 ";
		
		
   include ("delete_post.php");	
   
		     }			
			
		}
		
   
    //functon for searching category post ends here

  ?>
<!doctype html>
<html lang="en-US">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Light Theme</title>
<link href="css/singlePageTemplate.css" rel="stylesheet" type="text/css">
<!--The following script tag downloads a font from the Adobe Edge Web Fonts server for use within the web page. We recommend that you do not modify it.-->
<script>var __adobewebfontsappname__="dreamweaver"</script>
<script src="http://use.edgefonts.net/source-sans-pro:n2:default.js" type="text/javascript"></script>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
<?php 
	//start the user session, add database functions
require('db.php');

if(validateSession()){
    $currentUser = true;
}
else $currentUser = NULL;
	if(isset($_REQUEST['submit_comment'])){
	    $commentBody = $_POST['comment_body'];
        $postID = $_POST['post_id'];
        insertNewComment($postID, $commentBody);
    }




	?>

<!-- Main Container -->
<div class="container"> 
  <!-- Navigation -->
  <header> <a href="">
    <h4 class="logo">Blog Website</h4>
    </a>
    <nav>
      <ul>
        <li><a href="home.php">HOME</a></li>
        <li><a href="home.php#about">ABOUT</a></li>
          <?php
          //if the user is logged in, display the logout button on the nav bar
          if(isset($currentUser)){
              echo '<li> <a href="logout.php">LOG OUT</a></li>';
              echo '<li> <a href="change_password.php">CHANGE PASSWORD</a></li>';
          }else{
              echo '<li> <a href="register.php">REGISTER</a></li>';
              echo '<li> <a href="login.php">SIGN IN</a></li>';

          }
          ?>
      </ul>
    </nav>
  </header>
  <!-- Hero Section -->
<!--	  <section class="banner">-->
<!--    <h1 class="hero_mod_header">Blog Website<span class="light"></span></h1>-->
<!--    <p class="tagline">Term Project For CSC 435</p>-->
<!--	</section>-->
	<section class="view_post_banner">
		<?php
				//this php script is in charge of displaying the correct post that was selected by the user.
				//it obtains the post_id from the hidden form in the home.php page
				//the post_id is set using a javascript function to fill out the form with the valid data
		
				//get the post_id of the requested post
                if(isset($_GET['post_id'])){
                    $selectedPost = $_GET['post_id'];
                }
                else $selectedPost = $_POST['post_id'];



				//get the post information by the postID
				$result= getSinglePostByID($selectedPost);
	  			while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		  			//retrieve the title, author, description, and body corresponding to the post
					$title = $row[0];
		  			$description = $row[1];
		  			$author = $row[2];
					$body = $row[3];
					//display the post title, author
		  			echo '<h1 class="headers_post">'.$title.'</h1>';
					echo '</br>';
		  			echo '<h2 class="headers_post_author">By '.$author.'</h2>';
					echo '</br>';
					//display the body of the post
		  			echo '<p id="post_body">'.$body.'</p>';
					echo '</br>';
	
			}
		?>
		
	</section>

    <?php
    //display the create post section
    //hidden by javascript function showPost()
    echo '<section class="create_post">';
    echo '<h1 class="headers_post">Please Click On the Button to Comment on the Post</h1>';
    echo '<button class="button_post" id="create_post_btn" onClick="showPost()">CREATE COMMENT</button>';
    echo '<form id="CreatePostForm" enctype="multipart/form-data" action="viewpost.php" method="post">';
    echo '<h2 class="headers_post">Enter Your Comment Below: </h2></br>';
    echo '<input class="post_input" type="text" name="comment_body" placeholder="Limit of 50 Characters" maxlength="50" required /></br>';
    echo '<input id="post_id" name="post_id" type="hidden" value="'.$selectedPost.'"/>';
    echo '<input type="submit" class="button_post" name="submit_comment" value="submit"/>
</form>
	</section>';
    echo '<div class="gallery">';

    ?>

    <section class="view_post_banner">
        <h1 class="headers_post">Comments</h1>
        <?php
        $allComments = getAllComments($selectedPost);
        while ($row = mysqli_fetch_array($allComments, MYSQLI_NUM)) {
            //retrieve the comment body, usernanme of the commenter, and the timestamp of the comment from the array
            $commentBody = $row[0];
            $username = $row[1];
            $timestamp = $row[2];

            echo '<p id="post_body">'.$commentBody.'';
            echo '</br>';
            echo '---- '.$username.'</p>';

        }
        ?>

    </section>
  <!-- Stats Gallery Section -->
  <!-- Parallax Section -->
  <section class="banner">
    
    <p class="parallax_description">Final Project for Network Security</p>
  </section>
  <!-- More Info Section -->
  <footer></footer>

  <!-- Copyrights Section -->
  <div class="copyright"></div>
</div>
<!-- Main Container Ends -->

</body>
<script type="text/javascript">
    //javascript function to show the 'create posts' form
    const postForm = document.getElementById('CreatePostForm');
    postForm.style.display = "none";

    /**
     * Function to show the 'Create Posts' Form.
     * Will Change 'CREATE POST' button to 'CANCEL'
     */
    function showPost(){
        const btn = document.getElementById('create_post_btn');
        if(postForm.style.display !== "none"){
            postForm.style.display = "none";
            postForm.style.visibility = "hidden";
            btn.innerHTML = "CREATE COMMENT";
        }else{
            postForm.style.display = "block";
            postForm.style.visibility = "visible";
            btn.innerHTML = "CANCEL";
        }
    }
</script>

</html>

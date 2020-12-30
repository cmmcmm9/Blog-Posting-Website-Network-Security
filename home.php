
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
$cookieName = 'compsecp1';
require_once 'db.php';

//if(!isset($_COOKIE[$cookieName])) {
//    echo "Cookie named '" . $cookieName . "' is not set!";
//} else {
//    echo "Cookie '" . $cookieName . "' is set!<br>";
//    echo "Value is: " . $_COOKIE[$cookieName];
//}


//if the user is logged in, update info
if(validateSession()){
    $userName = getUsernameByID(getCookieContentsAsArray()["userID"]);
    echo '<script defer>window.alert("Welome back '.$userName.'!"); </script>';
    $currentUser = true;
}
else{
    echo '<script defer>window.alert("You are not logged in.");</script>';
}

	//this php is responsible for submitting the user data from the 'create posts' form
if (isset($_REQUEST['submit_post'])){
    $post_title = $_POST['title'];
    $post_descr = $_POST['description'];
    $post_body = $_POST['body'];

    //see insertNewPost() in db.php
    insertNewPost($post_title, $post_descr, $post_body);
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
        <li><a href="#about">ABOUT</a></li>
        
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
	  <section class="banner">
    <h1 class="hero_mod_header">Blog Website<span class="light"></span></h1>
    <p class="tagline">Term Project For CSC 435</p>
	</section>
  <!-- About Section -->
  <section class="about" id="about">
    <h2 class="hidden">About</h2>
    <p class="text_column">This website is the term project for CSC 435.</p>
    <p class="text_column">&nbsp;</p>
  </section>
<?php
	//display the create post section
    //hidden by javascript function showPost()
echo '<section class="create_post">';
echo '<h1 class="headers_post">Please Click On the Button to Create a Post</h1>';
echo '<button class="button_post" id="create_post_btn" onClick="showPost()">Create Post</button>';
echo '<form id="CreatePostForm" enctype="multipart/form-data" action="home.php" method="post">';
echo '<h2 class="headers_post">Enter The Title of Your Post: </h2></br>';
echo '<input class="post_input" type="text" name="title" placeholder="Limit of 50 Characters" maxlength="50" required/></br>';
echo '<h2 class="headers_post">Enter a Short Description: </h2></br>';
echo '<input class="post_input" type="text" name="description" placeholder="Limit of 50 Characters" maxlength="50" required /></br>';
echo '<h2 class="headers_post">Please Enter the Body of your Post</h2></br>
	<textarea name="body" placeholder="Limit of 1,000 Characters" cols="200" rows="15" required></textarea></br>
  	<input type="submit" class="button_post" name="submit_post" value="submit"/>
</form>
	</section>';
echo '<div class="gallery">';

?>

    <?php
	//display the blog posts section
	//4 blog posts will be displayed in one row on the webpage

    //getAllPosts() is in db.php
      $allPosts= getAllPosts();
      $counter = 1;
      while ($row = mysqli_fetch_array($allPosts, MYSQLI_NUM)) {
          $title = $row[0];
          $descrip = $row[1];
          $author = $row[2];
          $postID = $row[3];

          //set post id as HTML id
          if($counter <= 4){
              echo '<div class="thumbnail" id="'.$postID.'" onClick="viewPost(this.id)"><h1 class="stats">'.$title.'</h1><h4>BY '.$author.'</h4><p>'.$descrip.'</p></div>';
              $counter++;
          }else{
              echo '</div>';
              echo '<div class="gallery"><div class="thumbnail" id="'.$postID.'" onClick="viewPost(this.id)"><h1 class="stats">'.$title.'</h1><h4>BY '.$author.'</h4><p>'.$descrip.'</p></div>';
              $counter = 1;
          }
	  }
      ?>
  </div>
  <!-- Parallax Section -->
  <section class="banner">
    <h2 class="parallax">CSC435</h2>
    <p class="parallax_description">Final Project for Network Security</p>
  </section>
  <!-- More Info Section -->
  <footer></footer>
  <div class="copyright"></div>
</div>
<form id="form_post_click" action="viewpost.php" method="get">
<input type=number name="post_id" id="post_id" />
<input type="submit" name="post_clicked" />
</form>
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
		    btn.innerHTML = "CREATE POST";
        }else{
		    postForm.style.display = "block";
		    postForm.style.visibility = "visible";
		    btn.innerHTML = "CANCEL";
        }
    }

    /**
     * Function that fills out a hidden form in order to make a GET request to the
     * viewpost.php page. Takes the postID set in the HTML div element to pass to the
     * viewpost.php page. I am sure there is a better way.. but it works.. lol.
     * @param postID
     */
    function viewPost(postID){
	    document.forms['form_post_click'].elements['post_id'].value = parseInt(postID);
	    document.forms['form_post_click'].submit();
    }
	

</script>

</html>

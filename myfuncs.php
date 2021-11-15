<?php

function dbConnect() {
    // Connect to azure
    $link = mysqli_connect("127.0.0.1", "azure", "6#vWHD_$", "mysql", "54102");
    
    // Connect to local
    //$link = mysqli_connect("127.0.0.1", "root", "root", "mysql");
    
    // Check connection
    if($link === false){
        die("ERROR (myfuncs.php): Could not connect. " . mysqli_connect_error());
    }
    return $link;
}

function saveUserId($id)
{
    session_start();
    $_SESSION["USER_ID"] = $id;
    $_SESSION["POST_ID"] = 0;
}
function getUserId()
{
    session_start();
    return $_SESSION["USER_ID"];
}

function forgetUserId()
{
    session_start();
    $_SESSION = array();
}

// Uses session to automatically get userID
function getUserArrayFromCurrentUser($link)
{
    session_start();
    $userID = $_SESSION["USER_ID"];
    
    $sql = "SELECT * FROM users WHERE ID='$userID'";
    $result = mysqli_query($link, $sql);
    $numRows = mysqli_num_rows($result);
    if ($numRows >= 1)
    {
        return $result->fetch_assoc();	// Read the Row from the Query
    }
    else
    {
        return null;
    }
}

function getPostFromID($link, $id)
{   
    $sql = "SELECT * FROM posts WHERE ID='$id'";
    $result = mysqli_query($link, $sql);
    $numRows = mysqli_num_rows($result);
    if ($numRows >= 1)
    {
        return $result->fetch_assoc();	// Read the Row from the Query
    }
    else
    {
        echo('(myfuncs.php) No post found in database');
        return null;
    }
}

function getUsersByFirstName($link, $pattern)
{
    $sql = "SELECT * FROM users WHERE FIRST_NAME LIKE '$pattern'";
    $result = mysqli_query($link, $sql);
    $numRows = mysqli_num_rows($result);
    if (mysqli_num_rows($result) > 0) {
        $index = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $users[$index] = array($row["ID"], $row["FIRST_NAME"], $row["LAST_NAME"]);

            ++$index;
        }

        return $users;
    }
    else {
        return null;
    }
}

function getAllUsers($link)
{
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    $sql = "SELECT FIRST_NAME, LAST_NAME FROM users";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        $index = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $users[$index] = array($row["ID"], $row["FIRST_NAME"], $row["LAST_NAME"]);

            ++$index;
        }

        return $users;
    }
    else {
        return null;
    }
}

function get_posts($link, $sql)
{
    $result = mysqli_query($link, $sql);
    $currentUser = getUserId();
    $numRows = mysqli_num_rows($result);

    if ($numRows >= 1)
    {
        foreach($result as $index => $i) {
            $sql = "SELECT ID, AUTHOR_ID, TITLE, TEXT FROM posts"; 
            
            $postID = $i["ID"];
            $text = $i["TEXT"];
            $title = $i["TITLE"];
            $authorID = $i["AUTHOR_ID"];
            $tag = $i["TAG"];
            $rating = $i["RATING"];
    
            // Get First and Last name of Author from AUTHOR_ID
            $sql = "SELECT ID, FIRST_NAME, LAST_NAME, EMAIL, USERNAME, PASSWORD FROM users WHERE ID='$authorID'";
            $i = mysqli_query($link, $sql);
            $row = $i->fetch_assoc();	// Read the Row from the Query
            $author = $row["FIRST_NAME"] . ' ' . $row["LAST_NAME"];
            
            // Get user rating preference for this post (if any)
            $sql = "SELECT * FROM ratings WHERE USER_ID='$currentUser' AND POST_ID='$postID'";
            $result = mysqli_query($link, $sql);
            $rating_row = $result->fetch_assoc();	// Read the Row from the Query
            $users_rating = $rating_row["RATING"];
            if ($users_rating == 1) $users_rating = '+1';
            if ($users_rating == -1) $users_rating = '-1';
            if ($users_rating == 0) $users_rating = '0';

            // Display result
            echo('<div class="card">'); ////// Start of card div
            echo ('<h2>'.$title.'</h2> <h5>by '.$author.'</h5> <p class="text">'.$text.'</p> <p class="info">Tag: '.$tag.'</p> <div class="info"> <p>Rating:&nbsp;</p> <p id='.$index.'>'.$rating.'</p> <p>&nbsp;('.$users_rating.')</p> </div>');

            
            // Display comment(s) for post
            $sql = "SELECT * FROM comments WHERE POST_ID='$postID'";
            $result = mysqli_query($link, $sql);
            $numCommentRows = mysqli_num_rows($result);

            echo ("<h4>Comments:</h4>");
            if ($numCommentRows > 0)
            {
                foreach($result as $b)
                {
                    $poster_name = $b["FIRST_NAME"] . ' ' . $b["LAST_NAME"];
                    $text = $b["TEXT"];
                    $date = $b["SUBMIT_DATE"];
        
                    echo ('<div class="comment">');
                    echo ('<h5 class="commenter">'.$poster_name.'</h5> <p class="comment">'.$text.'</p> <p class="commentdate">Submit Date: '.$date.'</p>');
                    echo (' </div>');
                }
            }
            else
            {
                echo ("This post has no comments yet.");
            }

            // Check to see if user is logged in
            if (getUserId() == null)
            {
                echo('</div>'); /////// End of card div
                continue;
            }

            // Upvote/Downvote buttons
            echo('<form action="ratingHandler.php" method="POST">');
            echo('<button type="submit" formmethod="post" class="button button1" name="Upvote" value="1">Upvote</button>
                <button type="submit" formmethod="post" class="button button2" name="Downvote" value="-1">Downvote</button>
                <input type="hidden" name="POST_ID" value="'.$postID.'"/>
                <input type="hidden" name="POSTER_ID" value="'.$currentUser.'"/>');
            echo('</form>');

            // Provide tools for user to comment
            echo('<form action="commentHandler.php" method="POST">');
            echo(' <textarea id="comment-area" name="comment-area" rows="4" cols="50">Leave a comment.</textarea><br>');
            echo(' <input type="submit" value="Submit Comment" />');
            echo(' <input type="hidden" name="POST_ID" value="'.$postID.'"/>');
            echo('</form>');

            // Allow moderators and higher to edit/delete ANY post
            if (getUserArrayFromCurrentUser($link)["PERMISSION_LEVEL"] >= 1)
            {
                
                echo('<form action="modPanel.php" method="POST">
                         <input type="hidden" name="Data" value="'.$postID.'"/>
                         <input type="submit" value="Edit in Mod Panel" />
                      </form>');
            }
            // Allow user to delete/edit their own post; we can just re-use the mod panel for this
            else if ($authorID == $currentUser)
            {
                echo('<form action="modPanel.php" method="POST">
                         <input type="hidden" name="Data" value="'.$postID.'"/>
                         <input type="submit" value="Edit Post" />
                      </form>');
            }
            echo('</div>'); /////// End of card div
        }
    }

    return $numRows;
}

function showAdditionalMenus()
{
	require_once('myfuncs.php');

	$link = dbConnect();

	if (getUserId() == null)
	{
        echo ('<a style="float:right" href="register.php">Register</a>');
        echo('<a style="float:right" href="login.php">Login</a>');
	}
	else
	{
        echo('<a style="float:right" href="logoutHandler.php">Sign Out</a>');
	}


	// Allow Admins to see Admin panel. WILL USE SOON
	if (getUserArrayFromCurrentUser($link)["PERMISSION_LEVEL"] == 2)
	{
		
		echo('<div class="group">
				<a href="adminPanel.php">Admin Panel</a>
			</div>');
	}
}

function viewAllPosts()
{
    $link = dbConnect();
    $sql = "SELECT * FROM posts";

    get_posts($link, $sql);

    // Close connection
    mysqli_close($link);
}

function createPost()
{

}

function gotoResultPage($message)
{
    session_start();
    $_SESSION["MESSAGE"] = $message;
    session_write_close();

    header('Location: resultPage.php');
    exit; // We are moving pages so we should exit the current page
}

function showSearchResults()
{
    $link = dbConnect();
    $numResults = 0;

    // Input
    $search = $_POST['Search'];
    $tags = $_POST['Tag'];

    // Adding the parenthesis to group the tags for an AND check with the query
    $tag_query = '(';
    for ($i = 0; $i < count($tags); $i++)
    {
        $tag_query .= "TAG='$tags[$i]'";

        // Don't add for last iteration
        if ($i < count($tags)-1)
            $tag_query .= " OR ";
    }
    $tag_query .= ")";

    // Check connection
    if($link === false){
        gotoResultPage("ERROR: Could not connect. " . mysqli_connect_error());
    }

    // microtime to get my execution time
    $msc = microtime(true);

    // Search by title button was pressed
    if (isset($_POST['SearchTitle']))
    {
        // Add our selected tags to the query
        $sql = "SELECT * FROM posts WHERE TITLE LIKE '%$search%' AND " . $tag_query;
        $numResults = get_posts($link, $sql);

        echo('<div class="card">');
        // We found nothing so let's suggest posts with a related title
        if ($numResults == 0)
        {
            // Add our selected tags to the query
            $sql = "SELECT * FROM posts WHERE TEXT LIKE '%$search%' AND " . $tag_query;
            $numResults = get_posts($link, $sql);
            $msc = microtime(true) - $msc;
            $statistics = "Number of posts found: " . $numResults . "<br>" . "Execution time: " . $msc . ' seconds<br>';
            if ($numResults > 0)
            {
                echo("Couldn't find a post with that title, showing relevant results. <br>" . $statistics);
            }
            else
            {
                echo("No posts were found! None of the keywords in the searchbar were found in the title or body of a post. <br>" . $statistics);
            }
        }
        // We found something!
        else
        {
            $msc = microtime(true) - $msc;
            $statistics = "Number of posts found: " . $numResults . "<br>" . "Execution time: " . $msc . ' seconds<br>';
            echo $statistics;
        }
        echo('</div>');
    }

    // Search by body button was pressed
    if (isset($_POST['SearchBody']))
    {
        // Add our selected tags to the query
        $sql = "SELECT * FROM posts WHERE TEXT LIKE '%$search%' AND " . $tag_query;
        $numResults = get_posts($link, $sql);

        echo('<div class="card">');
        // We found nothing so let's suggest posts with a related title
        if ($numResults == 0)
        {
            // Add our selected tags to the query
            $sql = "SELECT * FROM posts WHERE TITLE LIKE '%$search%' AND " . $tag_query;
            $numResults = get_posts($link, $sql);
            $msc = microtime(true) - $msc;
            $statistics = "Number of posts found: " . $numResults . "<br>" . "Execution time: " . $msc . ' seconds<br>';
            
            if ($numResults > 0)
            {
                echo("Couldn't find a post with that text in the body, showing relevant results. <br>" . $statistics);
            }
            else
            {
                echo("No posts were found! None of the keywords in the searchbar were found in the title or body of a post. <br>" . $statistics);
            }
        }
        // We found something!
        else
        {
            $msc = microtime(true) - $msc;
            $statistics = "Number of posts found: " . $numResults . "<br>" . "Execution time: " . $msc . ' seconds<br>';
            echo $statistics;
        }
        echo('</div>');
    }
}

function loadAdminPanel()
{
    $link = dbConnect();

    $userID = getUserId();
    
    
    
    // Check if user is signed in
    if (is_null($userID))
    {
        $message = "You must be logged in to do this.";
        gotoResultPage($message);
    }
    
    // Check if user is admin
    if (getUserArrayFromCurrentUser($link)["PERMISSION_LEVEL"] < 2)
    {
        $message = "You must be an Admin to access this.";
        gotoResultPage($message);
    }
    
    $sql = "SELECT ID, FIRST_NAME, LAST_NAME, EMAIL, PERMISSION_LEVEL FROM users";
    $result = mysqli_query($link, $sql);
    $numRows = mysqli_num_rows($result);
    echo('<div class="card">');
    echo nl2br('<form action="adminHandler.php" method="POST">');
    if ($numRows >= 1)
    {

        echo nl2br('<h1>--- Edit Users ---</h1>');
        foreach($result as $index => $i) {
            $sql = "SELECT ID, FIRST_NAME, LAST_NAME, EMAIL, PERMISSION_LEVEL FROM users";
            
            $id = $i["ID"];
            $firstName = $i["FIRST_NAME"];
            $lastName = $i["LAST_NAME"];
            $email = $i["EMAIL"];
            $permissionLevel = $i["PERMISSION_LEVEL"];
            
            // Display result
            echo('<div class="settings">');
            echo('<input type="hidden" name="person['.$index.'][ID]" value="'.$id.'">');
            echo('<label>First Name</label><input type="text" name="person['.$index.'][First Name]" value="'.$firstName.'">');
            echo('<label>Last Name</label><input type="text" name="person['.$index.'][Last Name]" value="'.$lastName.'">');
            echo('<label>Email</label><input type="text" name="person['.$index.'][Email]" value="'.$email.'">');
            echo('<label>Permission Level</label><input type="text" name="person['.$index.'][Permission Level]" value="'.$permissionLevel.'">');
            echo('</div>');
        }
    }
    echo('<input type="submit" value="Submit Changes">');
    echo('</form>');
    echo('</div>');

    echo('<div class="card">');
    echo('<h1>--- Statistics ---</h1>');
    echo("Total Users: " . getNumberOfUsers($link));
    echo('<br>');
    echo("-Number of Guests: " . getNumberOfGuests($link));
    echo('<br>');
    echo("-Number of Moderators: " . getNumberOfModerators($link));
    echo('<br>');
    echo("-Number of Admins: " . getNumberOfAdmins($link));
    echo('<br>');
    echo("Number of Posts: " . getNumberOfPosts($link));
    echo('</div>');
}

function loadModPanel()
{
    $link = dbConnect();

    $postID = $_POST['Data'];
    $row = getPostFromID($link, $postID);
    
    // Info about our post
    $text = $row["TEXT"];
    $title = $row["TITLE"];
    $authorID = $row["AUTHOR_ID"];
    $tag = $row["TAG"];
    
    // We need to get the author's first and last name again
    $sql = "SELECT ID, FIRST_NAME, LAST_NAME, EMAIL, USERNAME, PASSWORD FROM users WHERE ID='$authorID'";
    $result = mysqli_query($link, $sql);
    $row = $result->fetch_assoc();	// Read the Row from the Query
    $author = $row["FIRST_NAME"] . ' ' . $row["LAST_NAME"];
    
    
    // Display result
    echo("<h1>$title</h1> <h2>by $author</h2> <p>$text</p> <p id='tag'>Tag: $tag</p>");
    
    echo('<form action="modHandler.php" method="POST" onsubmit="if (confirm(\'Are you sure?\') == false) return false;">
			<div class="group">
				<label>Edit Title</label>
			</div>
			<div class="group">
				<input type="text" name="Title" value="'.$title.'"/>
			</div>
			<div class="group">
				<label>Edit Tag</label>
			</div>
			<div class="group">
				<input type="text" name="Tag" value="'.$tag.'"/>
			</div>
			<div class="group">
				<label>Edit Text</label>
			</div>
			<div class="group">
				<textarea id="textInput" name="Text" rows="4" cols="50">'.$text.'</textarea>
			</div>
		
        <input type="hidden" name="Data" value="'.$postID.'"/>
        <input type="submit" name="Submit" value="Submit Changes" />
        <input type="submit" name="Delete" value="Delete Post" />
      </form>');
}

?>
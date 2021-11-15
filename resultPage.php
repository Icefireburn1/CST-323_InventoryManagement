<?php 
session_start();
$message = $_SESSION["MESSAGE"];;
?>

<!doctype html>
<html>
    <head>
      <title>CST-323</title>
      <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="header">
            <h1>CST-323 Justin Gewecke</h1>
        </div>

        <div class="topnav">

			<?php require_once('myfuncs.php'); showAdditionalMenus(); ?>
        </div>

        <div class="row">
            <div class="leftcolumn">
                <div class="card">
                    <h2>Result</h2>
                    <?php echo('<p>'.$message.'</p>'); ?>
                </div>
            </div>

            <div class="rightcolumn">
                <div class="card">
                    <h2>About Me</h2>
                    <p>Hello, my name is Justin. Thank you for visiting my site!</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Website layout from <a href="https://www.w3schools.com/css/css_website_layout.asp">w3schools.com</a></p> 
        </div>

    </body>
</html>


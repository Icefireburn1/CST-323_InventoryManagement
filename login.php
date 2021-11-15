<?php require_once('./myfuncs.php')?>

<!doctype html>
<html>
    <head>
      <title>CST-323</title>
      <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="header">
            <h1>CST-126 Justin Gewecke</h1>
        </div>

        <div class="topnav">
            <a href="index.php">Main Menu</a>

			<?php showAdditionalMenus(); ?>
        </div>

        <div class="row">
            <div class="leftcolumn">
                <div class="card">
                    <form action="loginHandler.php" method="POST">
                        <div class="group">
                            <label>User Name</label>
                            <input class="register" type="text" name="Username"/>
                        </div>
                        <div class="group">
                            <label>Password</label>
                            <input class="register" type="password" name="Password"/>
                        </div>
                    
                        <input type="submit" value="Login" />
                    </form>
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
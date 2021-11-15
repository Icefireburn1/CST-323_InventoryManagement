<?php require_once('./myfuncs.php')?>

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
			<a href="index.php">Main Menu</a>
			<?php require_once('myfuncs.php'); showAdditionalMenus(); ?>
        </div>

        <div class="row">
            <div class="leftcolumn">
                <div class="card">
					<form action="registerHandler.php" method="POST">
							<div class="group">
								<label>First Name</label>
								<input class="register" type="text" name="FirstName"/>
							</div>
							<div class="group">
								<label>Last Name</label>
								<input class="register" type="text" name="LastName"/>
							</div>
							<div class="group">
								<label>Email</label>
								<input class="register" type="text" name="Email"/>
							</div>
							<div class="group">
								<label>Re-enter Email</label>
								<input class="register" type="text" name="ReenterEmail"/>
							</div>
							<div class="group">
								<label>Username</label>
								<input class="register" type="text" name="Username"/>
							</div>
							<div class="group">
								<label>Password</label>
								<input class="register" type="password" name="Password"/>
							</div>
							<input type="submit" value="Submit" />
					</form>
				</div>
				
				<div class="card">
					<h3>---Note---</h3>
					<p>Username will be case in-sensitive. Password will be case-sensitive </p>
					<p>Username and Password must contain at least 6 characters </p>
					<p>Username and Password must be within 50 characters </p>
					<p>Username and Password cannot contain the characters ' " / \ [ ] ( ) { }</p>
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
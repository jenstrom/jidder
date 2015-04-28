<?php
//@header("location: .");
//print "start page";
//storeMessage(1, "här skriver vi på svenska!!!", 0);
//fetchMessages($data = []);
?>


		<div class="container-fluid" id="container-startpage">
			<div id="content-startpage">
			<div class="row-startpage">
				<h1>Jidder</h1>
				<div class="col-xs-12 col-md-6">
						<div class="col-md-9 col-md-offset-2" id="login">
							<form class="form-horizontal" action="." method="post" name="login">
	  							<div class="form-group">
	   								<label for="inputEmail3" class="col-sm-2 control-label">Email</label>
	    							<div class="col-sm-10">
	      								<input type="email" class="form-control" name="emaillogin" placeholder="Email">
	    							</div>
	  							</div>
	  							<div class="form-group">
	    							<label for="inputPassword3" class="col-sm-2 control-label">Password</label>
	   								<div class="col-sm-10">
	      								<input type="password" class="form-control" name="pwdlogin" placeholder="Password">
	    							</div>
	  							</div>
							  	<div class="form-group">
							    	<div class="col-sm-offset-2 col-sm-10">
							      		<input type="submit" class="btn btn-default" value="Log in">
							    	</div>
							  	</div>
							</form>
							<p><?=$loginResult?></p>
						</div>
				</div>

				<div class="col-xs-12 col-md-6">
						<div class="col-md-9 col-md-offset-1" id="register">
							<form class="form-horizontal" action="." method="post" name="register">
	  							<div class="form-group">
	   								<label for="inputEmail3" class="col-sm-2 control-label">Email</label>
	    							<div class="col-sm-10">
	      								<input type="email" class="form-control" name="emailreg" placeholder="Email">
	    							</div>
	  							</div>
	  							<div class="form-group">
	    							<label for="inputPassword3" class="col-sm-2 control-label">Password</label>
	   								<div class="col-sm-10">
	      								<input type="password" class="form-control" name="pwdreg1" placeholder="Password">
	    							</div>
	  							</div>
	  							<div class="form-group">
	    							<label for="inputPassword3" class="col-sm-2 control-label">Repeat password</label>
	   								<div class="col-sm-10">
	      								<input type="password" class="form-control" name="pwdreg2" placeholder="Repeat Password">
	    							</div>
	  							</div>
	  							<div class="form-group">
	    							<label for="inputPassword3" class="col-sm-2 control-label">Nickname</label>
	   								<div class="col-sm-10">
	      								<input type="text" class="form-control" name="nickname" placeholder="Nickname">
	    							</div>
	  							</div>
							  	<div class="form-group">
							    	<div class="col-sm-offset-2 col-sm-10">
							      		<input type="submit" class="btn btn-default" value="Register">
							    	</div>
							  	</div>
							</form>
							<p><?=$regResult?></p>
						</div>
				</div>
			</div>
			</div>
		</div>
  	</body>
</html>
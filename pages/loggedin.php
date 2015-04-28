<?php
//@header("location: .");
//print "logged in";
//print_r($_SESSION);
//print_r($_POST);
//print_r($_GET);
//print $userId;

$page = 1;

if (isset($_GET["page"]))
{
	$page = $_GET["page"];
}

?>

		
			<div class="col-md-8 col-md-offset-2 content">
				<div class="row" id='write-message-area'>
					<div class="col-xs-12">
						<form action="." method="post">
						  <div class="form-group">
						    <label for="Message">Message</label>
						    <textarea name="message" class="form-control" maxlength="140" rows="3" placeholder="Enter message" id="message-input" ></textarea>
						  </div> 
						  <input type="submit" class="btn btn-default" value="Post">
						</form>
					</div>
				</div>
				<div class="row " id="message-area">
					<div class="col-xs-12 hundred">
						<?php printMessages($page); ?>
					</div>
				</div>
				<div class="row" id='pagination'>
					<div class="col-xs-12">
						<?php pagination(".?");?>
					</div>
				</div>
			</div>
		</div>
  	</body>
</html>

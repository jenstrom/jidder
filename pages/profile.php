<?php
//@header("location: .");
$profileNickname = $_GET["profile"];
$profileId = getUserId($profileNickname);
//print_r($_GET);
$page = 1;
//print_r($_FILES);
//print_r($_POST);
//print_r($_SESSION);
//print $feedback;




if (isset($_GET["page"]))
{
	$page = $_GET["page"];
}
?>


		
			<div class="content col-md-10 col-md-offset-1">
				<div class="row hundred">

					<div class="col-md-4 col-xs-4" id="profile-picture">

						<img src="./pics/<?=getUserProfilePic($profileId);?>">
						Change profile picture
						<form enctype="multipart/form-data" action=".?profile=<?=$profileNickname;?>" method="post">
							<input type="file" name="upload"/>
							<input type="submit"/>
							<?= $feedback; ?>
						</form>

					</div>

					<div class="col-md-8 col-xs-12 hundred">

						<div id="profile-message-area">
							<?php printMessages($page, [$profileId], true); ?>
						</div>
						<div>
							<?php pagination(".?profile={$profileNickname}", [$profileId], true); ?>
						</div>

					</div> 

				</div>
				
			</div>
		</div>
  	</body>
</html>
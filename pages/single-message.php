<?php
$messageDetails = $messageArray[$i];

$nickname 		= filter_var($messageDetails['nickname'], FILTER_SANITIZE_SPECIAL_CHARS);
$posted 		= $messageDetails['posted'];
$message 		= filter_var($messageDetails['message'], FILTER_SANITIZE_SPECIAL_CHARS);
$messageId		= $messageDetails['id'];
$replyText		= "";
$picture		= getUserProfilePic(getUserId($nickname));

if ($messageDetails['reply_to'] != 0)
{
	$replyTo = $messageDetails['reply_to'];
	$replyText = "In response to <a href=\".?message={$replyTo}\">this message<a>.";
}

?>

<div class='row message'>

	<div class='col-xs-2 message-avatar'>

		<img src='./pics/<?=$picture?>'>

	</div>

	<div class='col-xs-10 message-text'>

		<?=$replyText?>
		
		<div class='message-head'>
		
			<a href='.?profile=<?=$nickname?>'><?=$nickname?></a>
			<?=$posted?>
		
		</div>
		
		<p><?=$message?></p>
	</div>

<?php

if ($replies = fetchReplies($messageId))
{
	$replyCount 	= count($replies);
	$replyHtml	= "<li role='presentation'><a href=\"#replies{$messageId}\" aria-controls='replies' role='tab' data-toggle='tab'>Replies {$replyCount}</a></li>";

}

?>

	<div class='col-xs-10 col-xs-offset-2 message-foot'>
		<div role='tabpanel'>

			<ul class='list-inline' role='tablist'>

				<li role='presentation'><a href='#reply<?=$messageId?>' aria-controls='reply' role='tab' data-toggle='tab'>Reply</a></li>
				<?=$replyHtml?>

			</ul>

			<div class='tab-content'>

				<div role='tabpanel' class='tab-pane' id='reply<?=$messageId?>'>

					<form action='.' method='post'>

						<textarea name='message' class='form-control' maxlength='140' rows='3' placeholder='Enter reply' id='reply-input'></textarea>
				        <input type='hidden' name='reply' value='<?=$messageId?>'>
				        <input type='submit' class='btn btn-default' value='Reply'>

				    </form>

				</div>

				<div role='tabpanel' class='tab-pane' id='replies<?=$messageId?>'>

<?php

foreach ($replies as $replyDetails)
{

$posted			= $replyDetails['posted'];
$nickname 		= filter_var($replyDetails['nickname'], FILTER_SANITIZE_SPECIAL_CHARS);
$posted 		= $replyDetails['posted'];
$message 		= filter_var($replyDetails['message'], FILTER_SANITIZE_SPECIAL_CHARS);
$messageId		= $replyDetails['id'];
$picture		= getUserProfilePic(getUserId($nickname));

?>

					<div class='row reply'>

						<div class='col-xs-2 message-avatar'>

							<img src='./pics/<?=$picture?>'>

						</div>

						<div class='col-xs-10 message-text'>

							<div class='message-head'>

								<a href='.?profile<?=$nickname?>'><?=$nickname?></a>
								<?=$posted?>

							</div>

							<p><?=$message?></p>

						</div>
					</div>

<?php

}

?>
				</div>

			</div>

		</div>

	</div>

</div>




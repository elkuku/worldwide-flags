<?php
switch (getRequestVar('action'))
{
	case 'build':
		ob_start();

		include '../bin/makeflags.php';

		$tStamp        = time();
		$cssFileName   = __DIR__ . '/tmp/flags' . $tStamp . '.css';
		$imageFileName = __DIR__ . '/tmp/flags' . $tStamp . '.png';

		$flagList = getRequestVar('flags');

		// Do some hard coded filtering on file paths here
		$flagList = preg_replace('{[^\w\s\'.\-\/"]}x', '', $flagList);

		$flagsProcessedNum = (new MakeFlags())
			->create($flagList, $imageFileName, $cssFileName);

		// No more error producing codes below here !!!

		$error = ob_get_clean();

		$response          = new stdClass;
		$response->message = sprintf('%d Flag Images processed.', $flagsProcessedNum);
		$response->error   = $error;
		$response->css     = '';
		$response->image   = '';

		if (file_exists($cssFileName))
		{
			$response->css = implode('', file($cssFileName));
		}

		if (file_exists($imageFileName))
		{
			$response->image = base64_encode(file_get_contents($imageFileName));
		}

		echo json_encode($response);

		return;
		break;
}

function getRequestVar($name)
{
	$value = isset($_GET[$name]) ? $_GET[$name] : '';

	if (!$value)
	{
		$value = isset($_POST[$name]) ? $_POST[$name] : '';
	}

	return $value;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="World Wide Flag Images">
	<meta name="author" content="elkuku">
	<link rel="icon" href="favicon.ico">

	<title>World Wide Flag Images</title>

	<!-- Bootstrap core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="css/template.css" rel="stylesheet">
	<link href="css/flags.css" rel="stylesheet">

	<link rel="stylesheet" href="css/jstree/default/style.css"/>
</head>

<body>

<div class="modal fade" id="pleaseWaitDialog" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Processing</h4>
			</div>
			<div class="modal-body">
				<p>Please wait&hellip;</p>
			</div>
			<div class="modal-footer">
				<div class="progress">
					<div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar"
					     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
						<span class="sr-only">Please wait&hellip;</span>
					</div>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
			        aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">
				World Wide Flag Images
			</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#">About</a></li>
				<li><a href="#">Contact</a></li>
			</ul>
		</div><!--/.nav-collapse -->
	</div>
</nav>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-4" id="tree-container">
			<div id="jstree"></div>
		</div>
		<div class="col-md-4">
			<ol>
				<li>
					Select the desired flags on the left then click "Create It".
				</li>
				<li>
					Copy the image and the CSS code from the right
				</li>
			</ol>
			<button class="btn btn-success" id="createit">Create It</button>
			<div id="responseMessage" class="alert-success"></div>
			<div id="errorMessage" class="alert-danger"></div>
			<p>Please note that this is still</p>
			<h2 style="color: red;">WIP !!</h2>
			<div id="selectionMessage">You selected 0 items:</div>
			<div id="selectionContainer"></div>

			<hr/>

			<a class="btn btn-default" href="" id="permalink">Permalink</a>
			Doesn't work yet :(
			<div id="permalinkD"></div>
			<p>
				Note that you may also download the <b>complete collection</b>
				<a href="img/flags.png">flags.png</a>
				<a href="css/flags.css">flags.css</a>
			</p>


		</div>
		<div class="col-md-4">
			<h2>CSS Sprite</h2>
			<div id="resultImageContainer">
				<img src="img/1x1.png" id="resultImage"/>
			</div>
			<h2>CSS Code</h2>
			<textarea id="resultCss"></textarea>
		</div>
	</div>
</div><!-- /.container -->

<footer class="footer">
	<div class="container text-muted">
		All Flags courtesy of the <a href="http://forum.tsgk.com/viewtopic.php?t=4921">TSGK Clan Forum</a> -
		Made 2016 by <a href="https://github.com/elkuku/worldwide-flags">elkuku
			<img src="img/1x1.png" class="flag flag-02_miscellaneous-jolly_rogers-calico-jack-rackham"
			     alt="calico-jack-rackham" title="calico-jack-rackham"/>
		</a>
	</div>
</footer>

<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jstree/jstree.js"></script>
<script src="js/flag-icons.js"></script>
<script type="text/javascript">
	jQuery(function ($) {
		$(document).ajaxStop(function () {
			$('#pleaseWaitDialog').modal('hide');
		});
		$(document).ajaxStart(function () {
			$('#pleaseWaitDialog').modal();
		});
	});
</script>
</body>
</html>

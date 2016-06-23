<?php
$action = getRequestVar('action');

switch (getRequestVar('action'))
{
    case 'build':
        ob_start();

        $flags = getRequestVar('flags');

        // Do some hard coded filtering on file paths here
        $flags = preg_replace('{[^\w\s\'.\-\/"]}x', '', $flags );
        $error = ob_get_clean();
        echo 'AAA';
        echo $flags;
        echo 'AAA';

        $response = new stdClass;
        $response->message = $flags;
        $response->error = $error;

        echo json_encode($response);

        return;
        break;
}

function getRequestVar($name)
{
    return isset($_GET[$name]) ? $_GET[$name] : '';
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

    <!-- Custom styles for this template -->
    <link href="css/template.css" rel="stylesheet">
    <link href="css/flags.css" rel="stylesheet">

    <link rel="stylesheet" href="css/jstree/default/style.css"/>
</head>

<body>

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
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
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
            <h1 style="color:red">WIP !!</h1>

            <ol>
                <li>
                    Select the desired flags on the left then click "Create It".
                </li>
                <li>
                    Copy the image and the CSS code from the right
                </li>
            </ol>
            <button class="btn btn-success" id="createit">Create It</button>
            <br/>
            <div id="errorMessage"></div>
            <div id="selectionMessage">You selected 0 items:</div>
            <div id="selectionContainer"></div>
            <a href="" id="permalink">Permalink</a>
            <div id="permalinkD"></div>

        </div>
        <div class="col-md-4">
            .col-md-4
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

</body>
</html>

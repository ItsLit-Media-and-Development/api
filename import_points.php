<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 23/06/2018
 * Time: 01:15
 */

if(isset($_GET['id']) && $_GET['id'] != '') {
	$data = json_decode(file_get_contents('https://api.streamelements.com/kappa/v2/points/' . $_GET['id'] . '/top?limit=1000'), true);

	var_dump($data);

	//array is under $data['users'] and has 2 elememnts per record - username and points and is a numeric array 0 - 99
} else {
	?>
    <html>
    <head>
        <title>Streamelements Points Importer</title>
    </head>
    <body>
    <h1>Stream Elements Points Importer</h1>
    <h3>Please Enter your account ID:</h3>
    <p>
        You can find it at https://streamelements.com/dashboard/account/channels/
    </p>
    <img src="id.PNG" width="50%" height="50%"/>
    <br/>
    <hr/>
    <form action="" method="get">
        <input type="name" name="id"/>
        <br/>
        <input type="submit"/>
    </form>
    </body>
    </html>
	<?php
}
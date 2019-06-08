<?php

require_once "class/Mysql.php";
require_once "class/MysqlBreadcrumb.php";
require_once "class/MysqlDatabase.php";

$action = "";
$orderby = "";
$direction = "";

/** filter the keys */
$keys = array_keys($_GET);
foreach ($keys as $key)
{
	${$key} = $_GET[$key];
}

/* instantiation */
$db 		= new MysqlDatabase();
$mysql	= new Mysql($db);
$server = (isset($serverName) ? new MysqlServer($db, ['SCHEMA_NAME'=>$serverName]) : null);
$table = (isset($serverName) && isset($tableName) ? new MysqlTable($db, ['TABLE_SCHEMA'=>$serverName, 'TABLE_NAME'=>$tableName], $orderby, $direction) : null);
$mysqlBreadcrumb = new MysqlBreadcrumb($server, $table);

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<!-- font awesome -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
				
    <title>MysqlGenerator</title>
  </head>
  <body>
		<!-- if there is a message display it here -->
		<?php
		if (isset($msg))
		{
		?>
		<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
			<strong><?php echo $msg; ?></strong>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php 
		}
		?>
		<h1>MysqlGenerator</h1>	
		<!-- crumb -->
		<?php
		echo $mysqlBreadcrumb->createBreadcrumb();
		?>

		<!-- page -->
		<?php
		switch ($action)
		{
			case "insert":
				$table->showInsertPage();
				break;

			case "update":
				$table->showEditPage($id, $action);
				break;

			default:
				if (!isset($serverName)) 
				{
					?>
					<h2>Servers</h2>
					<?php		
					$servers = $mysql->getServers();

					/** server list */
					foreach ($servers as $server) 
					{
						echo $server->getUrl();
					}
				} else {

						if (!isset($tableName)) 
						{
							?>
							<h1>Tables</h1>
							<?php
							/** get the tables */
							$tables = $server->getTables();
							foreach ($tables as $table)
							{
								echo $table->getUrl();
							}
						} else {
							/** show the table rows */
							echo $table->showRecordsPage();
						}
				}
	}
	?>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
<?php
require_once "class/MysqlDatabase.php";
require_once "class/MysqlTable.php";
/** 
 * AlertTypes can be:
 * primary = blue
 * secondary = grey
 * success = green 
 * danger = red
 * warning = yellow
 * info = lightblue
 * light = white
 * dark = dark grey
 */


//print_r($_GET);
//print_r($_POST);

/** init section */
$action = "";
$db = new MysqlDatabase();

/** get the pparams, it's either or... */
$params = [];
if (!empty($_POST)) 
{
	$params = $_POST;
}
if (!empty($_GET)) 
{
	$params = $_GET;
}

/** filter the keys */
$keys = array_keys($params);
foreach ($keys as $key)
{
	${$key} = $params[$key];
}

/** init */
$sql  = "";

switch ($action)
{
	/** functional actions */
	case "create":
		$params["action"] = "insert";
		$queryString =  http_build_query($params);
		header('Location: index.php' . (isset($queryString) ? "?" . $queryString : ""));
		break;
	case "edit":
		$params["action"] = "update";
		$queryString =  http_build_query($params);
		header('Location: index.php' . (isset($queryString) ? "?" . $queryString : ""));
		break;

	/** database actions */
	case "update":
		/** update the record and return to the page you came from page */
		$mysqlTable = new MysqlTable($db, ['TABLE_SCHEMA' => $_GET['serverName'], 'TABLE_NAME' => $_GET['tableName']]);
		$mysqlTable->updateRecord($_GET);

		header('Location: index.php?' . http_build_query(['serverName' => $_GET['serverName'], 'tableName' => $_GET['tableName'], "msg"=>"record updated", "type"=>"success"]));
		break;

	case "insert":
		/** insert the record anmd return to the page you came from page */
		$mysqlTable = new MysqlTable($db, ['TABLE_SCHEMA' => $_GET['serverName'], 'TABLE_NAME' => $_GET['tableName']]);
		$mysqlTable->insertRecord($_GET);

		header('Location: index.php?' . http_build_query(['serverName' => $_GET['serverName'], 'tableName' => $_GET['tableName'], "msg"=>"record inserted", "type"=>"success"]));
		break;

	case "delete":
		/** delete the record anmd return to the main page */
		$mysqlTable = new MysqlTable($db, ['TABLE_SCHEMA' => $_GET['serverName'], 'TABLE_NAME' => $_GET['tableName']]);
		$mysqlTable->deleteRecord($id);

		header('Location: controller.php?' . http_build_query(['serverName' => $_GET['serverName'], 'tableName' => $_GET['tableName'], "msg"=>"record deleted", "type"=>"success"]));
		break;

	/** navigation */
	default:
		$queryString =  http_build_query($params);
		header("Location: index.php" . (isset($queryString) ? "?" . $queryString : ""));
		break;
}
?>
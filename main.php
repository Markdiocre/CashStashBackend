<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: X-Requested-With,  Origin, Content-Type,");
header("Access-Control-Max-Age: 86400");
// ini_set('display_errors',0);
date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

$rootPath = $_SERVER["DOCUMENT_ROOT"];
$apiPath = $rootPath . "/cashstash";

require_once($apiPath . '/configs/Connection.php');
require_once($apiPath . '/controllers/Path.php');

$db = new Connection();
$pdo = $db->connect();

$gm = new GlobalMethods($pdo);
$auth = new Auth($pdo, $gm);
$money = new Money($pdo, $gm, $auth);

$req = [];
if (isset($_REQUEST['request']))
    $req = explode('/', rtrim($_REQUEST['request'], '/'));
else $req = array("errorcatcher");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $state = array(
            "rem" => "Failed",
            "msg" => "No Available public API. Please Contect the system Administrator"
        );
        echo json_encode($state);
        http_response_code(403);
        break;
    case 'POST':
        $data_input = json_decode(file_get_contents("php://input"));
        require_once($apiPath . '/routes/Try.routes.php');
        require_once($apiPath . '/routes/Auth.routes.php');
        require_once($apiPath . '/routes/Money.routes.php');
        break;

    default:
        echo "albert";
        http_response_code(403);
        break;
}

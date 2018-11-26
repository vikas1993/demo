<?php

//including the required files
require_once '../include/DbOperations.php';


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';


//Creating a slim instance
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);
//$app = new \Slim\App;


//View item request
$app->get('/viewitemdetails',function(Request $req,  Response $res, $args = []) {
    //verifying required parameters
    if(!verifyRequiredParams(array('alias'),$req)){
        return;
    }
    
 
    //getting post values
    $alias = $req->getParam('alias');
    
 
    //Creating DbOperation object
    $db = new DbOperation();
 
    //Creating a response array
    $response = array();
 
    //If username password is correct
    if($db->isItemExists($alias)){
 
        //Getting user detail
        $student = $db->getItem($alias);
        //echoResponse(200,$student);
        //Generating response
        $response['error'] = false;
        $response['Name'] = $student['Name'];
        $response['Alias'] = $student['Alias'];
        $response['Parent_Group'] = $student['Parent_Group'];
        $response['Op_Stock'] = $student['Op_Stock'];
        $response['Unit'] = $student['Unit'];
 
    }else{
        //Generating response
        $response['error'] = true;
        $response['message'] = "Invalid item id or item not exist";
    }
 
    //Displaying the response
    echoResponse(200,$response);
});

//Login request
$app->get('/adminlogin',function(Request $req,  Response $res, $args = []) {
    //verifying required parameters
    if(!verifyRequiredParams(array('phone','password'),$req)){
        return;
    }
    //getting post values
    $phone = $req->getParam('phone');
    $password = $req->getParam('password');
    
 
    //Creating DbOperation object
    $db = new DbOperation();
 
    //Creating a response array
    $response = array();
 
    //If username password is correct
    if($db->studentLogin($phone,$password)){
        //Getting user detail
        $student = $db->getStudent($phone,$password);
        //echoResponse(200,$student);
        //Generating response
        $response['error'] = false;
        $response['id'] = $student['ID'];
        $response['name'] = $student['U_NAME'];
        $response['EMAIL'] = $student['EMAIL'];
        $response['MOBILE'] = $student['MOBILE'];
 
    }else{
        //Generating response
        $response['error'] = true;
        $response['message'] = "Invalid username or password";
    }
 
    //Displaying the response
    echoResponse(200,$response);
});


$app->get('/addtransaction', function (Request $req,  Response $res, $args = [])  {
 
   
    //Verifying the required parameters
    //echoResponse(200,'sd');
    //verifyRequiredParams(array('myvar'),$req);
    //$myvar = $req->getParam('myvar');
    //echo $myvar;

    //Creating a response array
    $response = array();
    verifyRequiredParams(array('mechanic_id','bar_code_id'),$req);
    //reading post parameters
    $mechanic_id = $req->getParam('mechanic_id');
    $bar_code_id = $req->getParam('bar_code_id');
    
 
    //Creating a DbOperation object
    $db = new DbOperation();
 
    //Calling the method createStudent to add student to the database
    $res = $db->addTransaction($mechanic_id,$bar_code_id);
 
    //If the result returned is 0 means success
    if ($res == 0) {
        //Making the response error false
        $response["error"] = false;
        //Adding a success message
        $response["message"] = "You have added points successfully";
        //Displaying response
        echoResponse(201, $response);
 
    //If the result returned is 1 means failure
    } else if ($res == 1) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while adding points";
        echoResponse(200, $response);
 
    //If the result returned is 2 means user already exist
    } else if ($res == 2) {
        $response["error"] = true;
        $response["message"] = "Sorry, this item already reedemed";
        echoResponse(200, $response);
    }
   
});
//this method will create a student
//the first parameter is the URL address that will be added at last to the root url
//The method is post

$app->get('/addItem', function (Request $req,  Response $res, $args = [])  {
 
   
    //Verifying the required parameters
    //echoResponse(200,'sd');
    //verifyRequiredParams(array('myvar'),$req);
    //$myvar = $req->getParam('myvar');
    //echo $myvar;

    //Creating a response array
    $response = array();
    verifyRequiredParams(array('name','alias','parent_group','op_stock','unit'),$req);
    //reading post parameters
    $name = $req->getParam('name');
    $alias = $req->getParam('alias');
    $parent_group = $req->getParam('parent_group');
    $op_stock = $req->getParam('op_stock');
    $unit = $req->getParam('unit');
    $garage_name = $req->getParam('garage_name');
    $box_number = $req->getParam('box_number');
 
    //Creating a DbOperation object
    $db = new DbOperation();
 
    //Calling the method createStudent to add student to the database
    $res = $db->addItem($name,$alias,$parent_group,$op_stock,$unit);
 
    //If the result returned is 0 means success
    if ($res == 0) {
        //Making the response error false
        $response["error"] = false;
        //Adding a success message
        $response["message"] = "You are successfully added item";
        //Displaying response
        echoResponse(201, $response);
 
    //If the result returned is 1 means failure
    } else if ($res == 1) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while adding item";
        echoResponse(200, $response);
 
    //If the result returned is 2 means user already exist
    } else if ($res == 2) {
        $response["error"] = true;
        $response["message"] = "Sorry, this item already existed";
        echoResponse(200, $response);
    }
   
});

//Method to display response
function echoResponse($status_code, $response)
{
    //Getting app instance
    //echo $status_code;
    echo json_encode($response);
}


function verifyRequiredParams($required_fields,$req)
{
    //Assuming there is no error
    $error = false;

    //Error fields are blank
    $error_fields = "";

    

    //Looping through all the parameters
    foreach ($required_fields as $field) {

       // echo "ok ".isset($request_params[$field]);
        //if any requred parameter is missing
        if (strlen(trim($req->getParam($field))) <= 0) {
            //error is true
            $error = true;

            //Concatnating the missing parameters in error fields
            $error_fields .= $field . ', ';
        }
    }

    //if there is a parameter missing then error is true
    if ($error) {
        //Creating response array
        $response = array();

        //Getting app instance
        

        //Adding values to response array
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';

        //Displaying response with error code 400
        echoResponse(400, $response);

        //Stopping the app
        //$app->stop();
        return false;
    }
    return true;
}

//Method to authenticate a student 
function authenticateStudent(\Slim\Route $route)
{
    //Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    //Verifying the headers
    if (isset($headers['Authorization'])) {

        //Creating a DatabaseOperation boject
        $db = new DbOperation();

        //Getting api key from header
        $api_key = $headers['Authorization'];

        //Validating apikey from database
        if (!$db->isValidStudent($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}
$app->run();
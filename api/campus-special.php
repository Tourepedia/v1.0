<?php

require_once 'NotORM.php';

$pdo = new PDO('mysql:dbname=tourepedia1;host=localhost','root', 'name');

$db = new NotORM($pdo);

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();



session_start();


$app->get("/trips(/:id)", function($id = null) use($app, $db){
  $response = array();

  if($id == null){
      $response = json_decode(file_get_contents("campus-special-places-info.json"));
  }else{
      $allTrips = (array) json_decode(file_get_contents("campus-special-places-all.json"));
      if(isset($allTrips[$id]))
        $response = $allTrips[$id];
  }

  $app->response()->header('Content-type', 'application/json');

  echo json_encode($response);

});

$app->post("/submit", function() use($app, $db){

    $response = array("planSubmited"=>false);

    $user = (array) json_decode($app->request()->getBody());

    if($db->campus_special()->insert($user)){
      $response['planSubmited'] = true;
      $to1 = "trips@tourepedia.com";
      $to2 = "anands2675@gmail.com";
      $from = "From: trips@tourepedia.com";
      $subject = "New trip from campus special";
      $body = json_encode($user);
      mail($to1, $subject, $body, $from);
      mail($to2, $subject, $body, $from);
    }

    $app->response()->header('Content-type', 'application/json');

    echo json_encode($response);
});

$app->run();

 ?>

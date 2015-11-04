<?php
require_once 'NotORM.php';
$pdo = new PDO('mysql:dbname=tourepedia1;host=localhost','root', 'name');
$db = new NotORM($pdo);
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

/**********************************  USER LOGIN SIGNUP CHECKING ******************************/

$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['user'])) {
            $app->redirect('/login.html');
        }
    };
};

session_start();
$app->post("/auth/process", function () use ($app, $db) {

    $array = (array) json_decode($app->request()->getBody());
//    print_r($array);
    $email = $array['email'];
    $pwd = md5($array['pwd']);
    $user = $db->users()->where('user_email', $email)->where('user_password',$pwd);
    $count = count($user);
    if($count == 1){
     $_SESSION['user'] = $email;
     $data = array( "loginStatus" => "success",
                        'userEmail' => $email
                    );
    }
        else{
            $data = array( "loginStatus" => "login failure"
                    );
        }
   $app->response()->header('Content-Type', 'application/json');
   echo json_encode($data);
});



$app->post("/register", function () use ($app, $db) {

    $data = array();
    $array = (array) json_decode($app->request()->getBody());
    $user = $db->users()->where('user_email', $array['email']);
    $count = count($user);
    $email = $array['email'];
    if($count == 1){
         $data = array( "registerStatus" => "already registered"
          );
    }
    else{
              $p = array( 'user_email'=> $array['email'] ,
                      'user_password'=> md5($array['pwd']) ,
                      'user_name'=> $array['fullName'] ,
                      'user_contact_no1' => $array['mobileNumber']
                     );
//                     echo $array['mobileNumber'];
          $user = $db->users()->insert($p);
          $data = array( "registerStatus" => "successfully registered",
                  'userEmail' =>  $array['email']
          );
          $_SESSION['user'] = $email;
    }
   $app->response()->header('Content-Type', 'application/json');
   echo json_encode($data);
});

$app->post("/jobApplication", function () use ($app, $db) {

    $data = array();
    $inputData = (array) json_decode($app->request()->getBody());
    $response = array("applicationSubmitted"=>false);
    if($inputData){
      $user = $db->careers()->insert($inputData);
      $response['applicationSubmitted'] = true;
    }

    $to = "careers@tourepedia.com";
    $subject = "Job Application for".$inputData['jobTitle'];
    $body = "Hi Sudhir, \n\n";
    $body .= "You have a job application for ". $inputData['jobTitle']. " with the following details \n";
    $body .= "Applicant Name: ". $inputData['name']."\n";
    $body .= "Applicant Email: ". $inputData['email']."\n";
    $body .= "Applicant Mobile Number: ". $inputData['mobileNumber']."\n";
    $body .= "Applicant Github Profile: ". $inputData['gitHub']."\n";
    $body .= "Applicant Linkedin Profile: ". $inputData['linkedin']."\n";
    $body .= "Applicant Past Experience: ". $inputData['pastExperience']."\n";

    $body .= "\n\n";

    $body .= "Best Regards \n Tourepedia Team";

    $from = "From: support@tourepedia.com";

    mail($to, $subject, $body, $from);

    $response["mail"] = $to.$subject.$body.$from;

    $app->response()->header('Content-Type', 'application/json');
    echo json_encode($response);

});

$app->get("/auth/isLoggedIn", function() use ($app){
  if(isset($_SESSION['user'])){
     $data = array( "loggedInStatus" => "true");
  }else{
    $data = array( "loggedInStatus" => "false");
  }

  $app->response()->header('Content-Type', 'application/json');
   echo json_encode($data);
});

$app->get("/auth/logout", function () use ($app) {
   unset($_SESSION['user']);
});

$app->get("/jobs", function() use ($app){

  echo file_get_contents("jobs.json");

});

// $app->get('/tourepedia/our_trips(/:place_id)',function($place_id) use ($app , $db){
//   if($place_id != null){
//       $data = array();
//     foreach($db->our_trips()->where('places_id',$place_id) as $p){
//       $data[] = array_map('utf8_encode', array(
//             'id' => $p["id"],
//             'trip_name' => $p['trip_name'],
//             'trip_img1' => $p['our_trips_img1'],
//             'trip_stay' => $p['trip_stay'],
//             'trip_timeSpan' => $p['trip_timeSpan'],
//             'trip_cost' => $p['trip_cost'],
//             'trip_place_about' => stripslashes($p['trip_place_about']            )
//             )
//       );
//       }
//   }
//
//   $app->response()->header('content-type','application/json');
//   echo json_encode(array('places_data'=>$data));
//
// });

$app->get('/user/userdata'  , $authenticate($app) ,function() use($app ,  $db){

  $p = $db->users()->where('user_email' ,$_SESSION['user'])->fetch();
  // $user_id = $q1['id'];

  $user_data = array(
    'user_email' => $_SESSION['user'],
    'user_name' => $p['user_name'],
    'user_contact_no1' => $p['user_contact_no1'],
    'user_contact_no2' => $p['user_contact_no2']

  );
    $app->response()->header('content-type','application/json');
    echo json_encode(array('our_trips'=>$user_data));
});

$app->get('/user/userTrips'  , $authenticate($app) ,function() use($app ,  $db){

  $p = $db->users()->where('user_email' ,$_SESSION['user'])->fetch();
  $user_id = $p['id'];
  $bookTrips = array();
  $planTrips = array();
  foreach ($db->trips()->where('users_id',$user_id) as $trip ) {
    if($trip['trip_booktype']=='Book'){
      $booked_trip = $db->trips_book()->where('trips_id',$trip['id'])->fetch();
      $trip_attractions = $db->trip_attractions()->where('trips_id',$trip['id']);
      $attractions = array();
      foreach ($trip_attractions as $attr ) {
         $attractions[] = array(
           'id' => $attr['id'],
           'attraction_id' => $attr['attractions_id']
         );
      }


      $bookTrips[] = array(
        "trip_id"=> $trip['id'] ,"places_id"=> $trip["places_id"] , "trip_place_name"=>$trip["trip_place_name"], "trip_booktype" => $trip["trip_booktype"] ,
        "trip_starting_date"=> $trip["trip_starting_date"],  "user_name" =>$trip["user_name"] ,  "user_email" =>  $trip[ "user_email"], "users_id"=> $trip[ "users_id"] ,
         "user_address"=> $trip["user_address"] , "user_contact_no1"=> $trip["user_contact_no1"],  "user_contact_no2"=>$trip[ "user_contact_no2"],  "isOrderOk"=>$trip["isOrderOk"], "isOrderDone"=> $trip["isOrderDone"],
          "our_plan_id" => $trip["our_plan_id"] ,   "no_of_persons" => $booked_trip["no_of_persons"] , "expect_total_budget"=>$booked_trip["expect_total_budget"], "trip_type" =>$booked_trip["trip_type"] ,
            "no_of_days"=>$booked_trip["no_of_days"], "origin_city"=>$booked_trip["origin_city"], "end_city"=>$booked_trip["end_city"], "returning_city"=>$booked_trip["returning_city"] ,
            "hotels" => $booked_trip["hotels"] , "travel"=> $booked_trip["travel"] , "amenities" => $booked_trip["amenities"] , "attractions"=> $attractions
          );
    }
    else if($trip['trip_booktype']=='Plan'){
      $planned_trip = $db->trips_plan()->where('trips_id',$trip['id'])->fetch();
      $trip_attractions = $db->trip_attractions()->where('trips_id',$trip['id']);
      $attractions = array();
      foreach ($trip_attractions as $attr ) {
         $attractions[] = array(
           'id' => $attr['id'],
           'attraction_id' => $attr['attractions_id']
         );
      }

      $planTrips[] = array(
        "trip_id"=> $trip['id'] ,"places_id"=> $trip["places_id"] , "trip_place_name"=>$trip["trip_place_name"], "trip_booktype" => $trip["trip_booktype"] ,
        "trip_starting_date"=> $trip["trip_starting_date"],  "user_name" =>$trip["user_name"] ,  "user_email" =>  $trip[ "user_email"], "users_id"=> $trip[ "users_id"] ,
         "user_address"=> $trip["user_address"] , "user_contact_no1"=> $trip["user_contact_no1"],  "user_contact_no2"=>$trip[ "user_contact_no2"],  "isOrderOk"=>$trip["isOrderOk"], "isOrderDone"=> $trip["isOrderDone"],
          "our_plan_id" => $trip["our_plan_id"] ,   "no_of_persons" => $planned_trip["no_of_persons"] , "no_of_days" => $planned_trip["no_of_days"] ,
           "price_to_pay" => $planned_trip["price_to_pay"]  , "attractions"=> $attractions

          );
    }
  }

   $app->response()->header('content-type','application/json');
    echo json_encode(array('plan_trips'=>$planTrips , 'book_trips' => $bookTrips));
});



$app->get('/our_trips(/:place_id)'  ,function($place_id) use($app ,  $db){
  if($place_id!= null)
  {
    $data = array();
    $query = $db->our_trips()->where('places_id',$place_id);
    foreach ($query as $p ) {
            $our_trips_id = $p['id'] ;
            $data[] = array_map('utf8_encode',  array(
            'our_trips_id' => $our_trips_id ,
             'our_trips_name' => $p['trip_name'],
            'our_trips_img1' => $p['our_trips_img1'],
            'our_trips_img2' => $p['our_trips_img2'],
            'our_trips_placeId' => $place_id ,
            'our_trips_timeSpan' => $p['trip_timeSpan'],
            'our_trips_cost' => $p['trip_cost']
          ));
    }
    $app->response()->header('content-type','application/json');
    echo json_encode(array('our_trips'=>$data));
  }
});

$app->get('/our_trips_data(/:our_trip_id)'  ,function($our_place_id) use($app ,  $db){
  if($our_place_id!= null)
  {
    $query = $db->our_trips()->where('id',$our_place_id);

     foreach ($query as $p ) {
            $our_trips_id = $p['id'] ;
            $sights = array();
             $travel = array();

           $query2 = $db->our_trips_sights()->where('our_trips_id',$our_trips_id);

           foreach($query2 as $q){
            // echo $q['id'] .$q['sight_title'] ."\n";
              $sights[] = array(
              'sight_id' => $q['id'],
              'sight_name' => $q['sight_title'],
              'sight_about' => $q['sight_names']

            );

           }
           $query2 = $db->our_trips_travel()->where('our_trips_id',$our_trips_id);

           foreach($query2 as $q){
            // echo $q['id'] .$q['sight_title'] ."\n";
              $travel[] = array(
              'travel_id' => $q['id'],
              'travel_method' => $q['travel_method'],
              'travel_about' => $q['travel_details']
               );

           }

            $data[] =  array(

            'our_trips_id' => $our_trips_id ,
            'our_trips_img1' => $p['our_trips_img1'],
            'our_trips_img2' => $p['our_trips_img2'],
            'our_trips_placeId' => $p['places_id'] ,
            'our_trips_about' => array_map('utf8_encode', array(stripslashes($p['trip_place_about']))),
            'our_trips_timeSpan' => $p['trip_timeSpan'],
            'our_trips_cost' => $p['trip_cost'],
            'our_trips_travel' => $travel,
            'our_trips_sights' => $sights

          );
    }

    $app->response()->header('content-type','application/json');
    echo json_encode(array('our_trips_data'=>$data));
  }
});



$app->post('/tourepedia/submitPlan' ,function() use($app ,  $db){

    $data = array();
    $array = (array) json_decode($app->request()->getBody());
    $attractionsList = (array)$array['attractionsList'];
    $plan = (array) ($array['plan']);
    $book = (array)  ($array['book']);

    $startingDateDay = $array['startingDateDay'];
    $startingDateMonth = $array['startingDateMonth'];
    $startingDateYear = $array['startingDateYear'];
    $planType = $array['planType'];
    $selectedPlace = $array['selectedPlace'];
    $journeyStartingDate = $array['journeyStartingDate'];
    $selectedPlaceId = $array['selectedPlaceId'];

    $ourPlanId = $array['ourPlanId'];

    if($planType=='Book'){
      $travelPref = $book['travelPref'];       $travelBy = $book['travelBy'];
      $fullName = $book['fullName'];
      $aboutMeUs = $book['aboutMeUs'];         $moreAboutTrip = $book['moreAboutTrip'];
      $hotelPref = $book['hotelPref'];         $amenitiesPref = $book['amenitiesPref'];
      $originCity = $book['originCity'];       $returningCity = $book['returningCity'];
      $typeOfTrip = $book['typeOfTrip'];       $tripDuration = $book['tripDuration'];
      $numOfPeople = $book['numOfPeople'];     $expectedBudget = $book['expectedBudget'];
      $mobileNumber = $book['mobileNumber'];   $altMobileNumber = $book['altMobileNumber'];
      $hotels = $book['hotels'];               $travel = $book['travel'];
      $amenities = $book['amenities'];
      $email = $book['email'];
      $user_id = 0;
       $q1 = $db->users()->where('user_email' , $_SESSION['user']);
      if(count($q1)==1)
        $user_id = $q1->fetch()['id'];

      $insert_trip = array( "places_id"=> $selectedPlaceId , "trip_place_name"=>$selectedPlace, "trip_booktype" => $planType ,
      "trip_starting_date"=> $journeyStartingDate,  "user_name" => $fullName ,  "user_email" =>  $email, "users_id"=> $user_id ,
       "user_address"=> '' , "user_contact_no1"=> $mobileNumber,  "user_contact_no2"=>$altMobileNumber,  "isOrderOk"=>0, "isOrderDone"=> 0, "our_plan_id" => $ourPlanId);
//       print_r($insert_trip);
       $trip = $db->trips()->insert($insert_trip);

       $trip_id =  $trip['id'];
//       echo $trip_id ;
      $insert_trip_book = array(
        "trips_id" => $trip_id , "no_of_persons"=>$numOfPeople , "expect_total_budget"=>$expectedBudget, "trip_type" =>$typeOfTrip ,
          "no_of_days"=>$tripDuration, "origin_city"=>$originCity, "end_city"=>$returningCity, "returning_city"=>$returningCity ,
          "hotels" => $hotels , "travel"=> $travel , "amenities" => $amenities , "aboutme" => $aboutMeUs ,"about_trip" => $moreAboutTrip
        );

      $trip = $db->trips_book()->insert($insert_trip_book);

      for ($i =0 ; $i< sizeOf($attractionsList) ;$i++) {
        $p = (array)$attractionsList[$i];
        $attraction = array(
              "trips_id" => $trip_id,
              "attractions_id" =>  $p['id'],
              "attraction_preference" => 0
        );
        $db->trip_attractions()->insert($attraction);
        }

    }
    else{
      $fullName = $plan['fullName'];          $mobileNumber = $plan['mobileNumber'];
      $altMobileNumber = $mobileNumber;       $tripDuration =   $plan['numOfDays'];
      $priceToPay = $plan['priceToPay'];      $numOfPeople = $plan['numOfPeople'];
      $address = (array)$plan['address'];

      $email = $plan['email'];
      $user_id = 0;
       $q1 = $db->users()->where('user_email' ,$_SESSION['user']);
      if(count($q1)==1)
        $user_id = $q1->fetch()['id'];

      $insert_trip = array( "places_id"=> $selectedPlaceId , "trip_place_name"=>$selectedPlace, "trip_booktype" => $planType ,
      "trip_starting_date"=> $journeyStartingDate,  "user_name" => $fullName ,  "user_email" =>  $email, "users_id"=> $user_id ,
       "user_address"=> $address , "user_contact_no1"=> $mobileNumber,  "user_contact_no2"=>$altMobileNumber,  "isOrderOk"=>0, "isOrderDone"=> 0,  "our_plan_id" => $ourPlanId);
  //       print_r($insert_trip);
       $trip = $db->trips()->insert($insert_trip);

       $trip_id =  $trip['id'];
  //       echo $trip_id ;
      $insert_trip_plan = array(
        "trips_id" => $trip_id , "no_of_persons"=>$numOfPeople ,
          "no_of_days"=>$tripDuration , "price_to_pay" => $priceToPay
        );

      $trip_plan = $db->trips_plan()->insert($insert_trip_plan);

      for ($i =0 ; $i< sizeOf($attractionsList) ;$i++) {
        $p = (array)$attractionsList[$i];
        $attraction = array(
              "trips_id" => $trip_id,
              "attractions_id" =>  $p['id'],
              "attraction_preference" => 0
        );
        $db->trip_attractions()->insert($attraction);
        }
    }


    $to = "anand@tourepedia.com"; // this is your Email address
    $to1 = "anands2675@gmail.com" ;
    $to2 = "maurya@tourepedia.com" ;


    $from = 'support@tourepedia.com'; // this is the sender's Email address
    $email = $_SESSION['user'];

    $subject = "New Trip Submitted";
    $subject2 = "New Trip Submitted";
    $message = $email . " booked a trip " . " of type ".$planType . " on " . $journeyStartingDate ." at " . date('H:i:s', time()) ."\n\n" ;

    $headers = "From:" . $from;

    mail($to,$subject,$message,$headers);
    mail($to1,$subject,$message,$headers);
    mail($to2,$subject,$message,$headers);

    $app->response()->header('content-type','application/json');
    echo json_encode(array('order_id'=>$data));

});


$app->get('/region/places(/:region_id)', function($region_id=null) use ($app, $db){
      if($region_id != null){
          $data = array();
        foreach($db->places()->where('place_region_id',$region_id) as $p){
          $data[] = array(
              'id' => $p["id"],
              'place_name' => $p["place_name"],
              'place_region_name' => $p["place_region_name"],
              'place_region_id' => $p["place_region_id"]
        );
        }
     }

      else{
        foreach($db->places() as $p){
          $data[] = array(
              'id' => $p["id"],
              'place_name' => $p["place_name"],
              'place_region_name' => $p["place_region_name"],
              'place_region_id' => $p["place_region_id"]
        );
      }
      }
      $app->response()->header('content-type','application/json');
    echo json_encode(array('places_data'=>$data));
});

$app->get('/places(/:place_id)', function($place_id=null) use ($app, $db){



    if($place_id != null){



        $data = null;

        $query =    $db->places()->where('id', intval($place_id)) ;

        // echo $query;

        foreach($query as $p){
              // $attractions = array();
              // $other_images = array();
              // foreach ($p->attractions() as $attraction) {
              //     $attractions[] = $attraction['id'];
              // }
              // foreach ($p->places_images() as $img) {
              //     $other_images[] = $img['image_name'];
              // }
              // $reviews = array();

              // foreach ($p->places_reviews() as $review) {
              //     $user_id = $review['users_id'];
              //    $user_name = $review->users['user_name'];

              //     $reviews[] = array(

              //           'id' => $review['id'],

              //           'user_comment' => $review['user_comment'],

              //           'rating' => $review['user_rating'],

              //           'user_name' =>  $user_name

              //       );
               // }
             $data[] = array_map('utf8_encode', array(
                'id' => $p["id"],
                'place_name' => $p["place_name"],
                'place_region_name' => $p["place_region_name"],
                'place_state' => $p["place_state"],
                'place_bestTime' => $p["places_best_visiting_time"],
                'place_feeling' => $p["place_feeling"],
                'place_avg_temp_winter' => $p["place_avg_temp_winter"],
                'place_avg_temp_summer' => $p["place_avg_temp_summer"],
                'place_sea_level' => $p["place_sea_level"],
                'place_idealNo_days' => $p["place_idealNo_days"],
                'place_ideal_budget' => $p["place_ideal_budget"],
                'place_img1' => $p["places_img1"],
                'place_img2' => $p["places_img2"],
                'place_latitude' => $p["place_latitude"],
                'place_longitude' => $p["place_longitude"],
                 'place_about' => stripslashes($p["place_about"]),
'place_near_by_places' => stripslashes($p["place_near_by_places"]),
                'place_overall_rating' => $p["place_overall_rating"]
          ));

//      print_r ($data);

        }
    } else {
    //   foreach($db->places() as $p){
     //        $attractions = array();
     //         $other_images = array();
      //         foreach ($p->attractions() as $attraction) {
       //             $attractions[] = $attraction['id'];
       //         } //         foreach ($p->places_images() as $img) {
       //             $other_images[] = $img['image_name'];
        //         }
        //         $reviews = array();
         //         foreach ($p->places_reviews() as $review) {
         //             $user_id = $review['users_id'];
          //            $user_name = $review->users['user_name'];
          //             $reviews[] = array(
           //                   'id' => $review['id'],
           //                   'user_comment' => $review['user_comment'],
           //                   'rating' => $review['user_rating'],
           //                   'user_name' =>  $user_name
           //               ); //         }
            //       $data[] = array(
            //           'id' => $p["id"],
            //           'place_name' => $p["place_name"],
             //           'place_region_name' => $p["place_region_name"],
             //           'place_state' => $p["place_state"],
             //           'place_avg_temp_winter' => $p["place_avg_temp_winter"],
              //           'place_avg_temp_summer' => $p["place_avg_temp_summer"],
               //           'place_sea_level' => $p["place_sea_level"],
               //           'place_idealNo_days' => $p["place_idealNo_days"],
               //           'place_ideal_budget' => $p["place_ideal_budget"],
               //           'place_img' => $p["place_img"],
                //           'place_latitude' => $p["place_latitude"],
                 //           'place_longitude' => $p["place_longitude"],
                  //           'place_about' => stripslashes($p["place_about"]),
                  //           'attractions' => $attractions ,
                   //           'place_other_images' => $other_images,
                    //           'place_overall_rating' => $p["place_overall_rating"] ,
                     //           'reviews' => $reviews //     );
                     //
                    // }
            }
            $app->response()->header('content-type','application/json');
            echo json_encode(array('places_data'=>$data));
});

$app->get('(/:place_id)/attractions', function($place_id=null) use ($app, $db){

   if($place_id!=null){
     $data= array();
     foreach($db->attractions()->where('places_id',$place_id) as $p){
       $data[] = array_map('utf8_encode', array(
         'id' => $p['id'],
         'attraction_name' => $p['attraction_name'],
         'attraction_places_id' => $p['places_id'],
         'attraction_about' => stripslashes($p['attraction_about']),
         'attraction_timeSpan' => $p['attraction_timeSpan'],
         'attraction_cost' => $p['attraction_cost'],
          'attraction_image' => $p['attraction_image'],
          'attraction_postalCode' => $p['attraction_postalCode'],
          'attraction_overall_rating' => $p['attraction_overall_rating'] )
          );
        }
      }
    else {
      $data= array();
      }
    $app->response()->header('content-type','application/json');
    echo json_encode(["attractions"=>$data]);
});

$app->get('/attractions(/:name)', function($name=null) use ($app, $db){
    if($name == null){
      $data = array();
      foreach($db->attractions() as $p){
         // array_push($data,$usages_area); // $other_images = array(); foreach ($p->attractions_images() as $img) { $other_images[] = $img['image_name']; } $reviews = array(); foreach ($p->attractions_reviews() as $review) { $user_id = $review['users_id']; $user_name = $review->users['user_name']; $reviews[] = array( 'id' => $review['id'], 'user_comment' => $review['user_comment'], 'rating' => $review['user_rating'], 'user_name' =>  $user_name ); } $data[] = array( 'id' => $p['id'], 'attraction_name' => $p['attraction_name'], 'attraction_places_id' => $p['places_id'], 'attraction_about' => stripslashes($p['attraction_about']), 'attraction_timeSpan' => $p['attraction_timeSpan'], 'attraction_cost' => $p['attraction_cost'], 'attraction_image' => $p['attraction_image'], 'attractions_other_images' => $other_images, 'attraction_postalCode' => $p['attraction_postalCode'], 'attraction_overall_rating' => $p['attraction_overall_rating'], 'reviews' => $reviews );
	      }
    }
    else {
        $data = null;
        foreach($db->attractions()->where('attraction_name', $name) as $p){
            $other_images = array();
              foreach ($p->attractions_images() as $img) {
                  $other_images[] = $img['image_name'];
              }

            $reviews = array();
            foreach ($p->attractions_reviews() as $review) {
                $user_id = $review['users_id'];
                $user_name = $review->users['user_name'];
                $reviews[] = array(
                        'id' => $review['id'],
                        'user_comment' => $review['user_comment'],
                        'rating' => $review['user_rating'],
                        'user_name' =>  $user_name
                    );
          }
            $data[] = array(
            		'id' => $p['id'],
            		'attraction_name' => $p['attraction_name'],
            		'attraction_places_id' => $p['places_id'],
                'attraction_about' => stripslashes($p['attraction_about']),
            		'attraction_timeSpan' => $p['attraction_timeSpan'],
            		'attraction_cost' => $p['attraction_cost'],
            		'attraction_image' => $p['attraction_image'],
                'attractions_other_images' => $other_images,
            		'attraction_postalCode' => $p['attraction_postalCode'],
                'attraction_overall_rating' => $p['attraction_overall_rating'],
                'reviews' => $reviews
            );

        }

    }
    $app->response()->header('content-type','application/json');
    echo json_encode(array('attractions_data'=>$data));
});
$app->run();

?>

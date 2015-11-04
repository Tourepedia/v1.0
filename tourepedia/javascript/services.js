/**
 * Created by Sudhir on 17-Jun-15.
 */

app.constant("apiRoot", "api/");

app.service('PlacesService', ['$http', function ($http) {

    this.placeInfo = function () {
        return $http.get('./api/slim.php/region/places');
    };

    this.placeDataForPlace = function (placeId) {
        return $http.get('api/slim.php/places/' + placeId);
    };
}]);


app.service('AttractionService', ['$http', function ($http) {

    this.getAttractionFor = function (placeId) {
        return $http.get('api/slim.php/' + placeId + '/attractions');
    };

    this.getRelatedPrePlannedAttractionFor = function (placeId) {
        return [];
    };
}]);

app.service('SubmitPlanService', ['$http', function($http){
    this.submitPlan = function(user){
        return $http.post('api/slim.php/tourepedia/submitPlan', user);
    };
}]);

app.service('OurPlansService',['$http', function(http){
    this.ourPlansForPlace = function(placeId) {
        return http.get('api/slim.php/our_trips/'+placeId);
    };
    this.ourPlanData = function (ourPlanId) {
        return http.get('api/slim.php/our_trips_data/'+ourPlanId);
    };
}]);

app.factory('LoginSignUpService', ['$http', 'SessionService', function ($http, SessionService) {
    return {
        login: function (user) {
            var data = {
                email: user.email,
                pwd: user.password
            };
            return $http.post('api/slim.php/auth/process', data);
        },
        logout: function () {
            SessionService.destroy('userId');
            return $http.get('api/slim.php/auth/logout');
        },
        isLoggedIn: function () {
            return $http.post('api/slim.php/auth/isLoggedIn');
        },
        signUp: function (newUser) {
            var data = {
                'email': newUser.email,
                'pwd': newUser.password,
                'fullName': newUser.fullName,
                'mobileNumber': newUser.mobileNumber
            };
            return $http.post('api/slim.php/register', data);
        }
    };
}]);

app.factory('SessionService', [function () {
    return {
        set: function (key, value) {
            return sessionStorage.setItem(key, value);
        },
        get: function (key) {
            return sessionStorage.getItem(key);
        },
        destroy: function (key) {
            return sessionStorage.removeItem(key);
        },
        hasKey: function (key) {
            return sessionStorage.getItem(key) != undefined;
        }
    };
}]);

app.factory('JobApplicationService', ['$http', function(http){
    return{
        apply: function(applicant){
            return http.post('api/slim.php/jobApplication', applicant);
        },
        getJobs: function(){
            return http.get('api/slim.php/jobs');
        }
    };
}]);

app.factory("CampusSpecialService", ['$http', function(http){

  return{
    allPlaces: function(){
      return http.get('api/campus-special.php/trips');
    },
    placeForId: function(id){
      return http.get('api/campus-special.php/trips/'+id);
    },
    submitPlan: function(data){
      return http.post('api/campus-special.php/submit', data);
    }
  };

}]);


app.factory('InstaBookService', ["$http", "apiRoot", function(http, apiRoot){
  return{
    submit: function(user){
      console.log(user);
      return http.post(apiRoot+"insta-book.php/submit", user);
    }
  }
}]);

/**
 * Created by Sudhir on 17-Jun-15.
 */
"use strict";
var app = angular.module("tourepedia", ['ui.router']);

app.config(['$stateProvider', '$urlRouterProvider',"$locationProvider", function($stateProvider, $urlRouterProvider, locationProvider){
    locationProvider.html5Mode({enabled: true, requireBase: false});
    $urlRouterProvider.otherwise('/');
    $stateProvider
        .state('home',{
            url:'/',
            views:{
                'place-info':{
                    templateUrl: './tourepedia/html/views/home.html'
                },
                'fix-plans':{
                    templateUrl:'./tourepedia/html/views/fix-plans.html'
                },
                'testimonials':{
                    templateUrl:'./tourepedia/html/views/testimonials.html'
                }
            }
        })
        .state('selectDestination',{
            url: '/select-destination',
            views:{
                'place-info':{
                    templateUrl: './tourepedia/html/views/select-destination.html'
                },
                'fix-plans':{
                    templateUrl:'./tourepedia/html/views/fix-plans.html'
                },
                'testimonials':{
                    templateUrl:'./tourepedia/html/views/testimonials.html'
                }
            }
        })
        .state('planATrip',{
            url: '/plan-a-trip',
            views:{
                'place-info':{
                    templateUrl: './tourepedia/html/views/plan-a-trip.html'
                },
                'planning-steps-view@planATrip':{
                    templateUrl: './tourepedia/html/views/add-attractions.html'
                },
                'fix-plans@':{ },
                'testimonials@':{}
            }
        })
        .state('planATrip.proceed',{
            url: '/:planningStage',
            views:{
                'planning-steps-view@planATrip':{
                    templateUrl: function($stateParams){
                        return './tourepedia/html/views/'+$stateParams.planningStage+'.html'
                    }
                }
            }
        })
        .state('planATrip.prePlansInfo',{
            url: '/pre-plans/:prePlan',
            views:{
                'planning-steps-view@planATrip':{
                    templateUrl: function($stateParams){
                        return './tourepedia/html/views/'+$stateParams.prePlan+'.html'
                    }
                }
            }
        })
        .state('aboutUs',{
            url: '/about-us',
            views:{
                'place-info':{
                    templateUrl: function(){
                        document.title = "About Us @Tourepedia";
                        return './tourepedia/html/views/about-us.html'
                    }
                }
            }
        })
        .state('samplePlan',{
            url: '/sample-plan',
            views:{
                'place-info':{
                    templateUrl: function(){
                        document.title = "Sample Plan @Tourepedia";
                        return './tourepedia/html/views/sample-plan.html'
                    }
                }
            }
        })
        .state('privacyPolicies',{
            url: '/privacy-policies',
            views:{
                'place-info':{
                    templateUrl: function(){
                        document.title = "Privacy Policies @Tourepedia";
                        return './tourepedia/html/views/privacy-policies.html'
                    }
                }
            }
        })
        .state('termConditions',{
            url: '/term-and-conditions',
            views:{
                'place-info':{
                    templateUrl: function(){
                        document.title = "Term & Conditions @Tourepedia";
                        return './tourepedia/html/views/terms-and-conditions.html'
                    }
                }
            }
        })
        .state('careers',{
            url:'/careers',
            views:{
                'place-info':{
                    templateUrl:function(){
                     document.title = "Careers @Tourepedia";
                     return './tourepedia/html/views/careers.html';
                    }
                }
            }
        })
        .state('campusSpecial', {
          url:'/campus-special',
          views:{
              'place-info':{
                  templateUrl:function(){
                   document.title = "Campus Special @Tourepedia";
                   return './tourepedia/html/views/campus.html';
                  }
              },
              'campus-special-options@campusSpecial':{
                  templateUrl:function(){
                   return './tourepedia/html/views/campus-special-options.html';
                  }
              },
              'campus-special-places@campusSpecial':{
                  templateUrl:function(){
                   return './tourepedia/html/views/campus-special-places.html';
                  }
              }
          }
        })
        .state('campusSpecial.place-info', {
          url:'/place/:id',
          views:{
              'campus-special-options':{},
              'campus-special-places':{
                  templateUrl:function(){
                  document.title = "Campus Special Place Info @Tourepedia";
                   return './tourepedia/html/views/campus-special-place-info.html';
                  }
              }
          }
        })
        .state('campusSpecial.book', {
          url:'/:id/book',
          views:{
            'campus-special-options':{},
            'campus-special-places':{
                templateUrl:function(){
                  document.title = "Campus Special Book @Tourepedia";
                  return './tourepedia/html/views/campus-special-book.html';
                }
            }
          }
        })
        .state('instaBook', {
          url:'/insta-book',
          views:{
              'place-info':{
                  templateUrl: function(){
                      document.title = "Insta Book @Tourepedia";
                      return './tourepedia/html/views/insta-book.html';
                  }
              }
          }
        })
        .state('campusAmbassador', {
          url:'/campus-ambassador',
          views:{
              'place-info':{
                  templateUrl: function(){
                      document.title = "Campus Ambassador @Tourepedia";
                      return './tourepedia/html/views/campus-ambassador.html';
                  }
              }
          }
        })
}]);


// run at the start
app.run(['$rootScope', function($rootScope){
    $rootScope.$on('$stateChangeStart', function(events, toState){
        $rootScope.hideLocation = toState.name.split('.')[0] == "campusSpecial";
        NProgress.start();
    });
    $rootScope.$on('$stateChangeSuccess', function(){
        NProgress.done();
        document.body.scrollTop = document.documentElement.scrollTop = 0;
    });
}]);

app.directive("myMenu", function(){
    return{
        restrict: 'E',
        templateUrl: './tourepedia/html/templates/menu.html'
    }
});

app.directive('myFooter', function(){
    return{
        restrict: 'E',
        templateUrl: './tourepedia/html/templates/footer.html'
    }
});
app.directive('myLogin', function(){
    return{
        restrict: 'E',
        templateUrl: './tourepedia/html/templates/login.html'
    }
});
app.directive('mySignup', function(){
    return{
        restrict: 'E',
        templateUrl: './tourepedia/html/templates/signup.html'
    }
});
app.directive('moveTop', function(){
    return{
        restrict: 'E',
        templateUrl: './assets/html/templates/move-top.html'
    }
});
app.directive('planCosts', function () {
    return{
        restrict: 'E',
        templateUrl: './tourepedia/html/templates/plan-costs.html'
    }
});

app.directive('errSrc', function() {
    return {
        link: function(scope, element, attrs) {
            element.bind('error', function() {
                if (attrs.src != attrs.errSrc) {
                    attrs.$set('src', attrs.errSrc);
                }
            });
        }
    };
});

// filter for our user application
app.filter('PlaceTypeFilter', function(){
    return function(input, placeTypeFilter){
        var filteredArray = [];
        if(placeTypeFilter.length === 0)
            return input;
        else{
            for(var i=0; i < input.length; i++){
                if(placeTypeFilter.indexOf(input[i].type) != -1){
                    filteredArray.push(input[i]);
                }
            }

        }
        return filteredArray;
    };
});
app.filter('PlacePopularityFilter', function(){
    return function(input, placePopularityFilter){
        var filteredArray = [];
        if(placePopularityFilter.length === 0)
            return input;
        else{
            for(var i=0; i < input.length; i++){
                if(placePopularityFilter.indexOf(input[i].popularity) != -1){
                    filteredArray.push(input[i]);
                }
            }

        }
        return filteredArray;
    };
});


app.filter('OfferFilter', function () {
   return function(input, offerFilter){
    var filteredArray = [];
    if(offerFilter.length == 0 || offerFilter[0] == false)
        return input
    else{
        for(var i = 0; i < input.length; i++){
            if(input[i].discountedPrice != ''){
                filteredArray.push(input[i]);
            }
        }
    }
    return filteredArray;
   };
});



var app = angular.module('RKVApp', ['ui.bootstrap']);

app.controller('RKVCtrl', ['$scope', '$http', '$sce', '$rootScope', '$window', '$uibModal',
function($scope, $http, $sce, $rootScope, $window, $uibModal){

  // INIT STATE
  var $ = jQuery;
  appScope = $scope;

  $scope.init = function(){
    $scope.people = {};
    $scope.rkvoters_data = {};

    // grab it from the window scope, set by the php template from the model
    $scope.rkvoters_config = rkvoters_config;

    $scope.listRequest = {
      'street_name' : 'Select Street...',
      'support_level' : '-',
      'only_active' : true,
      'type' : 'Active'
    };

    $scope.map = {
      root : 	'https://www.google.com/maps/embed/v1/place?' +
          'key=AIzaSyC6MLx8c1eQORx3uTNmL5RwXY761YSXaVs'
    }

    $scope.totals = [
      'active voters', 'contacts', 'supporters'
    ];

    $rootScope.contactType = 'Phone Call';
    $rootScope.callStatus = 'Connection';

    $scope.makeApiCall({
      api : 'getStreetList'
    }, function(response){

      $scope.rkvoters_data.streets = response;

      var turfs_placeholder = [
        {
            turfid : 0,
            turf_name : "placeholder",
            turf_description 	: "turf description",
            active_voters : 0,
            contacts : 0,
            supporters: 0
        }
      ];

      $scope.load_turfs(turfs_placeholder);

      $scope.load_streets($scope.rkvoters_data.streets);


      $('#loading_frame').fadeOut(400, function(){
          $scope.loadComponent('knocklist');
          $scope.$digest();
          $('#main_content').fadeIn(400);
      });


    })
  }

  // DATA MODEL
  $scope.load_turfs = function(turf_list){

    $scope.total_counts = {
      active_voters: 0,
      contacts: 0,
      likes: 0
    };
    $.each(turf_list, function(idx, turf){
      $scope.total_counts.active_voters += parseInt(turf.active_voters);
      $scope.total_counts.contacts += parseInt(turf.contacts);
      $scope.total_counts.likes += parseInt(turf.supporters);
    });

    $scope.turf_hash = {};
    $.each(turf_list, function(idx, turf){
      $scope.turf_hash[turf.turfid] = turf;
      turf.totals = {};
      $.each($scope.totals, function(i, total_name){
        if(total_name == 'active voters'){
          turf.active_voters = parseInt(turf.active_voters);
          var percent =  parseInt((turf.active_voters / $scope.total_counts.active_voters) * 100);
          turf.totals['active voters'] = {
            num: turf.active_voters,
            percent: percent + '%'
          }
        }
        else {
          turf[total_name] = parseInt(turf[total_name]);
          var percent = parseInt((turf[total_name] / turf.active_voters) * 100);
          turf.totals[total_name] = {
            num: turf[total_name],
            percent: percent + '%'
          }
        }
      });
    });

  }
//  $scope.load_turfs($scope.rkvoters_data.turfs);

  $scope.load_streets = function(street_list, openTurfs){
    $scope.streets = {};
    $.each(street_list, function(idx, street){
      var turf = $scope.turf_hash[street.turfid];

      if(!(street.turfid in $scope.streets)){
        $scope.streets[street.turfid] = {
          turf: turf,
          state : 'closed',
          toggle_command : 'open',
          streets: []
        }
        if(openTurfs && $.inArray(street.turfid, openTurfs) != -1){
          $scope.toggleStreetSet($scope.streets[street.turfid], true);
        }
      }
      street.totals = {};
      $.each($scope.totals, function(i, total_name){
        if(total_name == 'active voters'){
          street.active_voters = parseInt(street.active_voters);
          var percent = parseInt((street.active_voters / turf.active_voters) * 100);
          street.totals['active voters'] = {
            num: street.active_voters,
            percent: percent + '%'
          }
        }
        else {
          street[total_name] = parseInt(street[total_name]);
          var percent = parseInt((street[total_name] / street.active_voters) * 100);
          street.totals[total_name] = {
            num: street[total_name],
            percent: percent + '%'
          }
        }
      });
      $scope.streets[street.turfid].streets.push(street);
    });
  }

  $scope.load_knocklist = function(data){
    $scope.knocklist = {people: [], addresses: [], contacts: []};
    $scope.contactList = {};
    $scope.contactList.length_label = data.length + ' People';


    var current_addr = { address : '', residents : []};


    $.each(data, function(person_index, person){
      $scope.load_person(person);
      $scope.knocklist.people.push(person);

      if(person.support_level != 0) $scope.knocklist.contacts.push(person);

      // load address block
      var addr = person.stnum + ' ' + person.stname;
      if(addr != current_addr.address){
        if(current_addr.address != '') {
          $scope.knocklist.addresses.push(current_addr);
        }
        current_addr = {
          address : addr,
          stname : person.stname,
          stnum: person.stnum,
          residents : []
        };
      }
      current_addr.residents.push(person);
    });
    if(current_addr.address != '') {
      $scope.knocklist.addresses.push(current_addr);
    }

    console.log($scope.knocklist);

    // build multi-street object
    if($scope.viewMode == 'multi-sheet'){
      $scope.street_sets = [];
      var current_street = 'x';
      var street_index = 0;
      $.each($scope.knocklist.addresses, function(index, address){
        if(address.stname == '') return;
        if(address.stname != current_street){
          street_index++;
          current_street = address.stname;
          $scope.street_sets[street_index] = {
            street_name : address.stname,
            safeUrl : $sce.trustAsResourceUrl($scope.map.root +
                  '&q=' + address.stname + '+PORTLAND+ME'),
            addresses : []
          }
        }
        $scope.street_sets[street_index].addresses.push(address);
      });
    }
  }

  $scope.load_person = function(person){

    person.age = getAge(person.dob);

    person.address = person.stnum + ' ' + person.stname;
    person.residentLabel = '';
    if(person.unit != '') {
      person.address += ' - ' + person.unit;
      person.residentLabel += person.unit + ' - ';
    }
    person.residentLabel += person.firstname + ' ' + person.lastname +
                ' - ' + person.enroll + ' - ' + person.age;

    person.residentLabel = person.residentLabel.toUpperCase();

    if(person.General2015 == 1) {

      person.residentLabel += '*';
    }
    if(person.votedin2013 == 1) person.residentLabel += '*';

    if(!('active' in person)) person.active = true;


    if(!(person.rkid in $scope.people)){
      $scope.people[person.rkid] = {};
    }

    for(var field in person){
      $scope.people[person.rkid][field] = person[field];
    }

    if(person.neighbors){
      $.each(person.neighbors, function(k, neighbor){
        $scope.load_person(neighbor);
      })
    }

    return person;
  }

  $scope.reverse = function(){
    $scope.knocklist.people.reverse();
    $scope.knocklist.addresses.reverse();
  }



  // API
  $scope.makeApiCall = function(request, callback){
    request.access_token = $scope.rkvoters_config.access_token;
    request.campaign_slug = $scope.rkvoters_config.campaign_slug;
    $http({
      method: 'POST',
      url: $scope.rkvoters_config.api_url,
      data: request
    }).then(
      function successCallback(response) {
        if(!("error" in response.data)){
          callback(response.data);
          return;
        }
        $scope.handleApiError(response);
      },
      function errorCallback(response) {
        $scope.handleApiError(response);
      }
    );
  }

  $scope.handleApiError = function(response){
    console.log(response);
  }

  $scope.getAddress = function(address){
    $scope.listRequest = {
      street_name : address.stname,
      stnum : address.stnum
    }
    $scope.search();
  }

  $scope.search = function(){
    var request = {
      api: 'get_knocklist',
      listRequest: $scope.listRequest
    }

    // make sure that the latest request is the one that loads
    var currentReference = this;
    $scope.currentReference = currentReference;

    $scope.makeApiCall(request, function(response){
      delete $scope.listRequest.stnum;
      $scope.updateMap($scope.listRequest.street_name);
      if($scope.currentReference != currentReference) return;
      $scope.load_knocklist(response);
    });
  }

  $scope.updateTurfAssignment = function(street, oldTurfId){
    var request = {
      api: 'updateTurfAssignment',
      streetid: street.streetid,
      turfid: street.turfid
    }
    $scope.makeApiCall(request, function(streetlist){
      var openTurfs = [oldTurfId, street.turfid];
      $scope.load_streets(streetlist, openTurfs);
      $scope.$digest();
    });

  }

  $scope.updateTotals = function(){
    var request = {
      api: 'updateTotals'
    }
    $scope.makeApiCall(request, function(response){
      $scope.load_turfs(response.turfs);
      $scope.load_streets(response.streets);
      $scope.$digest();
    });

  }

  $scope.updateReportMode = function(viewMode){
    if(viewMode == 'fundraising'){
      var request = {
        api: 'getDonations'
      }
      $scope.makeApiCall(request, function(response){
        $scope.donationTotal = response[0].total + response[1].total + response[2].total;
        $scope.donationSets = response;
        $scope.$digest();
      });
    }
    if(viewMode == 'emailLocalSupporters'){
      var request = {
        api: 'getLocalSupporterEmails'
      }
      $scope.makeApiCall(request, function(response){
        $scope.localSupporterEmailList = response;
        $scope.$digest();
      });
    }
    if(viewMode == 'mailingList'){
      var request = {
        api: 'getMailingList'
      }
      $scope.makeApiCall(request, function(response){
        console.log(response.length);
        $scope.listLength = response.length;
        $scope.mailingList = response;
        $scope.$digest();
      });
    }


  }

  $scope.setSupport_Level = function(support_level, person){
    person.support_level = support_level;
    var request = {
      api : 'updatePerson',
      rkid : person.rkid,
      person : person,
      listRequest: appScope.listRequest
    }
    $scope.makeApiCall(request, function(response){
      $scope.load_knocklist(response);
    });
  }

  $scope.openStreet = function(street_name){
    $scope.listRequest.street_name = street_name;
    $scope.loadComponent('knocklist');
    $scope.search();
  }


  // UI CONTROLS
  $scope.loadComponent = function(componentName){

    $scope.component = componentName;
    if(componentName == 'report'){
      $scope.showMap = 1;
      $rootScope.viewMode = 'totals';
    }
    if(componentName == 'knocklist'){
      $scope.showMap = -1;
      $rootScope.viewMode = 'knocknotes';
    }

  }

  $scope.updateMap = function(street_name){
    if($scope.showMap == 1){
      var street_path = street_name + '+PORTLAND+ME';
      $scope.map.safeUrl = $sce.trustAsResourceUrl($scope.map.root + '&q=' + street_path);
    }
  }

  $scope.toggleMap = function(){
    $scope.showMap *= -1;
    if($scope.showMap == 1){
      $scope.updateMap($scope.listRequest.street_name);
    }
  }

  $scope.openPerson = function(person){

    if('knocklist' in $scope){
      $.each($scope.knocklist.people, function(index, row){
        if(row.rkid == person.rkid){
          $scope.selected_index = index;
        }
      });
    }

    var request = {
      api : 'getFullPerson',
      rkid : person.rkid
    }
    $scope.makeApiCall(request, function(person){

      $scope.load_person(person);
      $scope.featured_person = person;

      var modalInstance = $uibModal.open({
        controller: 'FeaturePersonCtrl',
        templateUrl: "modal_template.html"
      });

    });
  }

  $scope.openListManager = function(){
    $uibModal.open({
      template: $('#modal_listManager').html(),
      controller: 'ListManagerCtrl',
    });
  }

  $scope.openPersonAdder = function(){
    $rootScope.mode = 'Add';
    $uibModal.open({
      template: $('#modal_personAdder').html(),
      controller: 'PersonAdderCtrl'
    });
  }

  $scope.toggleStreetSet = function(turf, dontupdate){
    if(turf.state == 'closed'){
      turf.state = 'open';
      turf.toggle_command = 'close';
    }
    else {
      turf.state = 'closed';
      turf.toggle_command = 'open';
    }
    //if(!dontupdate) $scope.$digest();
  }

  // AND FIRE!!!
  $scope.init();


}
]);

app.controller('ListManagerCtrl',
['$scope', '$rootScope', '$uibModal',
  function($scope, $rootScope, $uibModal){

    $scope.litbomb = {};

    $scope.dropBomb = function(){
      if(confirm('Are you sure you mean to drop this bomb?')){
        var request = {
          api: 'litBomb',
          date: $scope.litbomb.date,
          rkids : [],
          listRequest: appScope.listRequest
        }
        $.each(appScope.knocklist.people, function(i, person){
          request.rkids.push(person.rkid);
        });
        $scope.makeApiCall(request, function(revisedList){
          appScope.load_knocklist(revisedList);
          $scope.$close();
        });
      }
    }

    $scope.sendPostcards = function(){
      if(confirm('Are the postcards in the mail?')){
        var request = {
          api: 'send_postcards',
          listRequest: appScope.listRequest
        }
        $scope.makeApiCall('', request, function(revisedList){
          appScope.load_knocklist(revisedList);
          $scope.$close();
        });
      }

    }

  }
]
);

app.controller('PersonAdderCtrl',
['$scope', '$rootScope', '$uibModal',
  function($scope, $rootScope, $uibModal){

    var st = appScope.listRequest.street_name;
    if(st == 'Select Street...') st = '';

    $scope.person = {
      stname : st,
      enroll: 'U',
      active: 1,
      city: 'Portland',
      state: 'ME'
    };

    $scope.savePerson = function(){
      var request = {
        api: 'addPerson',
        person: $scope.person,
        listRequest: appScope.listRequest
      }
      $scope.makeApiCall('', request, function(revisedList){
        appScope.load_knocklist(revisedList);
        $scope.$close();
      });
    }


  }
]
);

app.controller('FeaturePersonCtrl',
['$scope', '$uibModal', '$rootScope',
  function($scope, $uibModal, $rootScope,){

    $scope.person = appScope.featured_person;
    $scope.newContact = {
      type : 'Post Card'
    };

    $scope.editBasicInfo = function(){
      $scope.$close();
      $rootScope.mode = 'Edit';
      $uibModal.open({
        template: "modal_personAdder.html",
        controller: 'FeaturePersonCtrl'
      });
    }

    $scope.goBack = function(){
      $scope.$close();
      $uibModal.open({
        template: "modal_template.html",
        controller: 'FeaturePersonCtrl',
      });
    }

    // update person
    $scope.savePerson = function(mode){
      $scope.person.active = 1;
      var request = {
        api : 'updatePerson',
        rkid : $scope.person.rkid,
        person : $scope.person,
        listRequest: appScope.listRequest
      }
      appScope.makeApiCall(request, function(person){
        $scope.person = person;
        appScope.load_person(person);
        if(mode == 1) $scope.$close();
        if(mode == 2) $scope.openNext();
      });
    }

    // record contact
    $scope.recordContact = function(progress){
      $scope.newContact.rkid = $scope.person.rkid;
      $scope.newContact.type = $rootScope.contactType;
      $scope.newContact.status = $rootScope.callStatus;

      var request = {
        api : 'recordContact',
        rkid : $scope.person.rkid,
        contact : $scope.newContact,
        person : $scope.person
      }
      appScope.makeApiCall(request, function(person){
        $scope.newContact = { };
        $scope.person = appScope.load_person(person);

        if(progress) $scope.openNext();
      });
    }

    // open next person
    $scope.openNext = function(){
      $scope.$close();

      var i = appScope.selected_index;
      i++;
      if(i == appScope.knocklist.people.length){
        i = 0;
      }
      appScope.selected_index = i;
      var p = appScope.knocklist.people[i];
      appScope.openPerson(p);
    }

    // open prev person
    $scope.openPrev = function(){
      $scope.$close();

      var i = appScope.selected_index;
      i--;
      if(i == -1){
        i = appScope.knocklist.people.length - 1;
      }
      appScope.selected_index = i;
      var p = appScope.knocklist.people[i];
      appScope.openPerson(p);
    }

    // remove
    $scope.removePerson = function(){
      if(confirm('Are you sure you want to remove this person?')){
        var request = {
          api : 'removePerson',
          rkid : $scope.person.rkid,
          listRequest: appScope.listRequest
        }
        appScope.makeApiCall(request, function(response){
          if(response.status == 'deleted'){
            appScope.load_knocklist(response.knocklist);
            $scope.$close();
          }
        });
      }
    }

    // delete contact
    $scope.deleteContact = function(contact){
      var request = {
        api : 'deleteContact',
        vc_id : contact.vc_id,
        rkid: contact.rkid
      }
      appScope.makeApiCall(request, function(person){
        $scope.newContact = {};
        $scope.person = person;
        appScope.load_person(person);
      });
    }
  }
]
);


// UTILITIES
function getAge(dob){
  var now = new Date();
  var dob = new Date(dob);
  var diff = now.getTime() - dob.getTime();
  var ms_inayear = 1000 * 60 * 60 * 24 * 365;
  return parseInt(diff / ms_inayear)
}

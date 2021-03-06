
<script>
	// load server-side data
	<?php
		global $rkvoters_model;
		echo 'var rkvoters_config = ' . json_encode($rkvoters_model -> get_clientdata());
	?>;
</script>

<div id="loading_frame">
	Loading...
</div>

<div id="main_content"  style="display: none;"  ng-app="RKVApp" ng-controller="RKVCtrl">
	<div class="innerFrame">
		<div class="interface_controller" style="text-align: left;">
			<a ng-click="loadComponent('report')" ng-if="component=='knocklist'">&lt; &lt; See Report</a>
			<a ng-click="loadComponent('knocklist')"  ng-if="component=='report'">
				&lt; &lt; See Knocklists
			</a>
		</div>

		<div ng-if="component=='knocklist'">
			<div class="searchFilter">
				<select ng-model="listRequest.street_name">
					<option>Select Street...</option>
					<optgroup ng-repeat="turf in streets" label="{{turf.turf.turf_name}}">
						<option ng-repeat="street in turf.streets">{{street.street_name}}</option>
					</optgroup>
				</select>
				<input ng-model="listRequest.search_str" ng-change="search()" placeholder="Search..." />
				<select ng-model="listRequest.type">
					<option>All</option>
					<option>Active</option>
					<option>Donors</option>
					<option>Volunteers</option>
					<option>Phones</option>
					<option>Phones - Open</option>
					<option>Phones - Not Called</option>
					<option>Phones (Not Anchor) - Not Called</option>
					<option>Phones - Called</option>
					<option>Need Postcards</option>
					<option>Sent Postcards</option>
					<option>Seniors - Phones</option>
					<option>Seniors - Phones - Not Called</option>
					<option>Active Under 35</option>
					<option>Active Under 35 - with phones</option>
					<option>West End - Super - No Contact</option>
					<option>Parkside - Phones</option>
				</select>
				<select ng-model="listRequest.party">
					<option>-</option>
					<option>G</option>
					<option>D</option>
					<option>R</option>
				</select>
				<select ng-model="listRequest.status">
					<option>-</option>
					<option>0</option>
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
					<option>5</option>
				</select>
				<button ng-click="search()">SEARCH</button>
				<button ng-click="reverse()">REVERSE</button>
				<button ng-click="openPersonAdder()">ADD PERSON</button>
				<button ng-click="toggleMap()">MAP</button>

				<div style="padding-top: 15px; text-align: right">
					<a ng-click="openListManager()">{{contactList.length_label}}</a>
				</div>
			</div>

			<div class="mapFrame" ng-if="showMap == 1">
				<iframe
				  width="100%"
				  height="300"
				  frameborder="0" style="border:0"
				  ng-src="{{map.safeUrl}}" allowfullscreen>
				</iframe>
			</div>

			<div class="results clearfix">

				<!-- "INDIVIDUALS" mode - should probably remove altogether
				<div ng-if="$root.viewMode == 'individuals'">
					<div class="result col-sm-4 clearfix" ng-repeat="person in knocklist.people"
							ng-click="openPerson(person, $index)" ng-if="person.active">
						<div style="float: left; margin-right: 20px;" class="left_info">
							<div class="status status{{person.support_level}}"><span>{{person.support_level}}</span></div>
							<div class="interaction_count">3</div>
						</div>
						<div style="float: left;">
							<b>{{person.firstname}} {{person.lastname}}</b>
							<br />- {{person.phone}}
							<br />- {{person.enroll}} - {{person.age}}
						</div>
					</div>
				</div> -->

				<!-- MARK ABSENTEES
				<div ng-if="$root.viewMode == 'mark absentees'">
					<div ng-repeat="address in knocklist.addresses">
						<div class="result addressResult col-sm-4 clearfix">
							<b ng-click="getAddress(address)">{{address.address}}</b>
							<div ng-repeat="person in address.residents"
								ng-if="person.active">
								<div class="person{{person.support_level}}">
									<span class="status{{person.support_level}}">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</span>
									&nbsp;
									<span ng-click="openPerson(person, $index)">{{person.residentLabel}}</span>
									- <span ng-click="setStatus(4, person)" class="addressResident" >P</span>
									- <span ng-click="setStatus(5, person)" class="addressResident" >V</span>
								</div>
							</div>
						</div>
					</div>
				</div> -->

				<!-- "ADDRESSES" mode - should probably remove altogether
				<div ng-if="$root.viewMode == 'addresses'">
					<div ng-repeat="address in knocklist.addresses">

						<div class="result addressResult col-sm-4 clearfix">
							<b ng-click="getAddress(address)">{{address.address}}</b>
							<div ng-repeat="person in address.residents"
								class="addressResident" ng-if="person.active">
								<div ng-click="openPerson(person)" class="person{{person.support_level}}">
									<span class="status{{person.support_level}}">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</span>
									&nbsp;{{person.residentLabel}}
								</div>
							</div>
						</div>

						<div class="clearfix" ng-if="$index % 3 == 2"></div>

					</div>

					<div ng-repeat="person in knocklist.contacts" style="clear: both; font-size: 11px">
						<br /><br />
						<b style="text-transform: uppercase;" ng-click="openPerson(person, $index)">{{person.firstname}} {{person.lastname}}</b> - {{person.enroll}} - {{person.age}}

						<br />Status: {{person.support_level}}
						<br />{{person.bio}}
					</div>
				</div>
			</div>-->

			<div ng-if="$root.viewMode == 'knocknotes'">


				<div ng-repeat="address in knocklist.addresses" style="clear:both;" class="result clearfix">

					<div class="addressResult col-sm-4 clearfix">
						<b ng-click="getAddress(address)">{{address.address}}</b>
						<div ng-repeat="person in address.residents"
							class="addressResident" ng-if="person.active">
							<div ng-click="openPerson(person)" class="person{{person.support_level}}">
								<span class="status{{person.support_level}}">&nbsp;&nbsp;&nbsp;&nbsp;</span>
								&nbsp;{{person.residentLabel}}
							</div>
						</div>
					</div>

					<div class="col-sm-8" style="font-size: 11px">
						<div ng-repeat="person in address.residents" ng-if="person.support_level != 0 || person.bio != ''">
							<b style="text-transform: uppercase;">{{person.firstname}} {{person.lastname}}</b>
							<span ng-if="person.phone != ''"> - {{person.phone}}</span>
							<br />{{person.support_level}} - {{person.bio}}
							<br /><br />
						</div>
					</div>
					<div class="clearfix" ng-if="$index % 3 == 2"></div>
				</div>
			</div>

			<div ng-if="$root.viewMode == 'multi-sheet'">
				<div ng-repeat="street in street_sets" style="clear:both; page-break-inside: auto" class="result clearfix">

					<h3>{{street.street_name}}</h3>
					<iframe
					  width="100%"
					  height="300"
					  frameborder="0" style="border:0"
					  ng-src="{{street.safeUrl}}" allowfullscreen>
					</iframe>

					<div ng-repeat="address in street.addresses" style="clear:both;" class="result clearfix">
						<div class="addressResult col-sm-5 clearfix">
							<b ng-click="getAddress(address)">{{address.address}}</b>
							<div ng-repeat="person in address.residents"
								class="addressResident" ng-if="person.active">
								<div ng-click="openPerson(person)" class="person{{person.support_level}}">
									<span class="status{{person.support_level}}">
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</span>
									&nbsp;{{person.residentLabel}}
								</div>
							</div>
						</div>

						<div class="col-sm-7" style="font-size: 11px">
							<div ng-repeat="person in address.residents" ng-if="person.support_level != 0">
								<b style="text-transform: uppercase;">{{person.firstname}} {{person.lastname}}</b>
								<br />Status: {{person.support_level}} - {{person.bio}}
								<br /><br />
							</div>
						</div>
						<div class="clearfix" ng-if="$index % 3 == 2"></div>
					</div>

					<div style="page-break-after: always;">&nbsp;&nbsp;</div>
				</div>
			</div>

		</div>


		<div ng-if="component=='report'" class="clearfix report_frame" style="clear: both;">
			<div class="row" style="margin: 15px 0 30px;">
				<div class="col-md-6">
					<div style="float: left;">
						<select ng-model="viewMode" ng-change="updateReportMode(viewMode)">
							<option value="totals">VIEW TOTALS</option>
							<option value="assigner">REDRAW DISTRICTS</option>
							<option value="fundraising">FUNDRAISING REPORT</option>
							<option value="emailLocalSupporters">EMAIL LOCAL SUPPORTERS</option>
							<option value="mailingList">MAILING LIST</option>
						</select>
						<button ng-click="updateTotals()">UPDATE TOTALS</button>
					</div>
					<form action="" target="_blank" style="float: left; margin-left: 5px;">
						<input type="hidden" name="export" value="emails" />
						<input type="submit" value="EXPORT EMAILS"  />
					</form>
				</div>
				<form class="col-md-6" style="text-align: right;" >
					{{total_counts.active_voters|number}} Voters |
					{{total_counts.contacts|number}} Contacts |
					{{total_counts.likes|number}} Likes
					({{ (100 * total_counts.likes / total_counts.contacts) |number}}%)
				</form>
			</div>
			<div class="turf_assigner col-md-9" ng-if="viewMode == 'assigner'">
				<div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-9">
						<div ng-repeat="turf in turf_hash" class="col-md-2" style="padding: 0 3px">
							<i style="font-size: 11px;">{{turf.turf_name}}</i>
						</div>
					</div>
				</div>

				<div ng-repeat="turfObj in streets" style="margin-bottom: 15px;">
					<div>
						<b>{{turfObj.turf.turf_name}}</b>
						- <a ng-click="toggleStreetSet(turfObj)">{{turfObj.toggle_command}}</a>
					</div>
					<div style="max-height: 400px; overflow-y: scroll; overflow-x: hidden;
							border: solid 1px #ccc; margin: 5px 0; padding: 5px;" ng-if="turfObj.state == 'open'">
						<div class="row" ng-repeat="street in turfObj.streets"
								style="padding: 10px 0; border-bottom: solid 1px #ccc">
							<div class="col-md-3">
								<a ng-click="updateMap(street.street_name)">
									{{street.street_name}}
								</a>
							</div>
							<div class="col-md-9">
								<div ng-repeat="turf in turf_hash" class="col-md-2">
									<input 	type="radio" name="street_{{street.streetid}}"
											ng-model="street.turfid"
											value="{{turf.turfid}}"
											ng-checked="street.turfid == turf.turfid"
											ng-change="updateTurfAssignment(street, turfObj.turf.turfid)" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="turf_totals col-md-9" ng-if="viewMode == 'totals'">
				<div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-9">
						<div ng-repeat="total in totals" class="col-md-4"
							style="padding: 0 3px 5px; text-align: center;">
							<i style="font-size: 13px; text-transform: uppercase; font-weight: bold">{{total}}</i>
						</div>
					</div>
				</div>

				<div ng-repeat="turfObj in streets" style="margin-bottom: 15px;">
					<div class="row">
						<div class="col-md-3">
							<b>{{turfObj.turf.turf_name}}</b>
							- <a ng-click="toggleStreetSet(turfObj)">{{turfObj.toggle_command}}</a>
						</div>
						<div class="col-md-9" style="text-align: center;">
							<div ng-repeat="total in turfObj.turf.totals" class="col-md-4">
								<div class="col-md-6">{{total.num | number}}</div>
								<div class="col-md-6">{{total.percent}}</div>
							</div>
						</div>
					</div>
					<div style="max-height: 400px; overflow-y: scroll; overflow-x: hidden;
								border: solid 1px #ccc; margin: 5px 0; padding: 5px; background: #f5f5f5"
						ng-if="turfObj.state == 'open'">

						<div class="row" ng-repeat="street in turfObj.streets"
								style="padding: 10px 0; border-bottom: solid 1px #ccc">
							<div class="col-md-3">
								<a ng-click="openStreet(street.street_name)">
									{{street.street_name}}
								</a>
								- <a ng-click="updateMap(street.street_name)">MAP</a>
							</div>
							<div class="col-md-9" style="text-align: center;">
								<div ng-repeat="total in street.totals" class="col-md-4">
									<div class="col-md-6">{{total.num}}</div>
									<div class="col-md-6">{{total.percent}}</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="turf_totals col-md-9" ng-if="viewMode == 'fundraising'">

				<div style="font-size: 16px; margin-bottom: 40px;">
					<span style="font-weight: bold;">TOTAL: </span>
					<span>{{donationTotal | currency}}</span>
				</div>

				<div style="font-size: 16px; margin-bottom: 40px;">
					<a href="?export=donors" target="_blank">Download</a>
				</div>


				<div ng-repeat="donationSet in donationSets">
					<div style="font-weight: bold; font-size: 16px;">{{donationSet.title}}</div>
					<div class="row clearfix"
						 ng-repeat="donation in donationSet.donors" style="margin: 3px 0;">
						<div style="width: 80px; float: left; ">
							{{donation.amount | currency}}
						</div>
						<div style="width: 80px; float: left; ">
							{{ donation.datetime }}
						</div>
						<div style="float: left;">
							<a ng-click="openPerson(donation)">
								{{donation.firstname}} {{donation.lastname}}
							</a>
						</div>
					</div>

					<div class="row" >
						<div class="col-md-6" style="border-top: solid 1px #ccc;">
							<i>{{donationSet.total | currency}}</i>
						</div>
					</div>
					<br /><br />
				</div>
			</div>
			<div class="turf_totals col-md-9" ng-if="viewMode == 'emailLocalSupporters'">
				{{localSupporterEmailList}}
			</div>
			<div class="turf_totals col-md-9" ng-if="viewMode == 'mailingList'">
				{{listLength | number}} Records<br /><br />
				<a href="?export=mailinglist">DOWNLOAD</a><br /><br />
				<div ng-repeat="address in mailingList">
					<b>{{address.name}}</b> <br />{{address.address}}<br /><br />
				</div>
			</div>

			<div class="map_frame col-md-3">
				<iframe
				  width="100%"
				  height="600"
				  frameborder="0" style="border:0"
				  ng-src="{{map.safeUrl}}" allowfullscreen>
				</iframe>
			</div>
		</div>

	</div>

	<!-- MODAL TEMPLATES -->
	<div style="display: none;">

		<!-- PERSON MODAL (PRIMARY) -->
		<script type="text/ng-template" id="modal_template.html">
			<div class="modalFrame clearfix">
				<div class="col-md-6">
					<b>{{person.firstname}} {{person.lastname}}</b>
					- <a ng-click="editBasicInfo()">EDIT</a>
					<br />- {{person.address}}, {{person.city}} {{person.state}} {{person.zip}}
					<br />- {{person.age}} - {{person.enroll}}
					<span ng-if="person.votedin2011 == 1"> - 2011</span>
					<span ng-if="person.votedin2013 == 1"> - 2013</span>
				</div>
				<div class="col-md-6" style="text-align: right;">
					<div style="padding: 0 5px 10px;">
						<a ng-click="openPrev()">&lt;&lt;</a>
						&nbsp;&nbsp;&nbsp;
						<a ng-click="openNext()">&gt;&gt;</a>
					</div>
					<div style="clear: both;">
						<button ng-click="removePerson()" style="float: right">Remove from List</button>
					</div>
				</div>
				<div class="topSection clearfix" style="clear:both;">
					<div class="col-md-4  field">
						<b>Support Level:</b>
						<br />
						<select ng-model="person.support_level">
							<option value="0">0 - Unidentified</option>
							<option value="1">1 - With Us</option>
							<option value="2">2 - Undecided</option>
							<option value="3">3 - Against Us</option>
							<option value="4">4 - Pending Absentee</option>
							<option value="5">5 - Voted Absentee</option>
						</select>
					</div>
					<div class="col-md-4  field">
						<b>Email:</b>
						<br />
						<input placeholder="Email" ng-model="person.email" />
					</div>
					<div class="col-md-4  field">
						<b>Phone
							<span ng-if="person.phoneType != ''">({{person.phoneType}})</span>
							<a ng-if="person.phone != ''" href="tel:{{person.phone}}">call</a>
							:
						</b>
						<br />
						<input placeholder="Phone" ng-model="person.phone" />
					</div>
					<div class="col-md-12 field">
						<b>Bio:</b>
						<br />
						<textarea ng-model="person.bio" style="height: 60px;"></textarea>
					</div>
					<div class="col-md-6">
						<label>
							<input id="volunteer_check" ng-model="person.volunteer" type="checkbox" style="width: 20px"
									 ng-true-value="'true'" ng-false-value="''"/>&nbsp;
							Volunteer
						</label>
						<br /><br />
						<button ng-click="savePerson(1)">Save & Close</button>
						<button ng-click="savePerson(2)">Save & Next</button>
					</div>
					<div class="col-md-6" ng-if="person.neighbors">
						<br />
						<b>Folks at the same number \ address:</b>
						<ul>
							<li ng-repeat="neighbor in person.neighbors">
								{{neighbor.support_level}} - {{neighbor.residentLabel}} -  - <i>{{neighbor.bio}}</i>
							</li>
						</ul>
					</div>



				</div>
				<div class="bottomSection">
					<div class="col-md-6">
						<div>
							<select ng-model="person.closed">
								<option value="0">Person is Open</option>
								<option value="1">Person is Closed</option>
							</select>
							<br /><br />
						</div>

						<h2>Add Contact</h2>
						<b>Type:</b>
						<select ng-model="$root.contactType">
							<option>Chat at Door</option>
							<option>Chat on Street</option>
							<option>Chat Elsewhere</option>
							<option>Donation</option>
							<option>Email</option>
							<option>Lit Drop</option>
							<option>Phone Call</option>
							<option>Post Card</option>
							<option>Sent Post Card</option>
							<option>Update</option>
						</select>

						<br /><br />

						<b>Date:</b>
						<br />

						<input
						type="text" class="form-control"
						is-open="focus" ng-focus="focus=true"
						uib-datepicker-popup="shortDate"
						ng-model="newContact.datetime"
						close-text="Close"
						placeholder="Enter date..." />

						<div ng-if="$root.contactType == 'Donation'">
							<br />
							<b>Amount:</b>
							<br /><input type="number" ng-model="newContact.amount" />
						</div>

						<br />
						<b>Note:</b>
						<select ng-if="$root.contactType == 'Phone Call'" ng-model="$root.callStatus"
								style="margin: 0 0 10px">
							<option>Connection</option>
							<option>VM - Person Confirmed - Message</option>
							<option>VM - Not Confirmed - Message</option>
							<option>VM - Person Confirmed - No Message</option>
							<option>VM - Not Confirmed - No Message</option>
							<option>Just Ringing</option>
							<option>Bad Number</option>
						</select>
						<br />
						<textarea ng-model="newContact.note"></textarea>

						<br /><br />
						<button ng-click="recordContact()">Post</button>
						&nbsp;&nbsp; <button ng-click="recordContact(1)">Post & Next</button>

					</div>


					<div class="col-md-6" style="border-left: dashed 1px #ccc;">
						<h2>Log</h2>
						<div ng-repeat="contact in person.contacts">
							<i>{{contact.datetime.split(' ')[0]}}</i> -
							<a ng-click="deleteContact(contact)">X</a>
							<br /><b>{{contact.type}}</b>
							<span ng-if="contact.status != ''">
								 - {{contact.status}}
							</span>
							<span ng-if="contact.note != ''">
								 - {{contact.note}}
							</span>
							<span ng-if="contact.amount != 0">
								${{contact.amount}}
							</span>
							<span ng-if="contact.agent != 0">
								<i>- {{contact.username}}</i>
							</span>
							<br /><br />
						</div>
					</div>

				</div>
			</div>
		</script>

		<!-- LIST MANAGER MODAL -->
		<script type="text/ng-template" id="modal_listManager">
			<div class="modalFrame clearfix">

				<div class="col-md-6">
					<b>View Mode?</b>
					<br />
					<select ng-model="$root.viewMode">
						<option>addresses</option>
						<option>individuals</option>
						<option>knocknotes</option>
						<option>mark absentees</option>
						<option>multi-sheet</option>
					</select>
				</div>



				<div class="col-md-6">
					<b>Drop a Lit Bomb on this street?</b>
					<br />
					<input
						type="text" class="form-control"
						datepicker-popup="shortDate"
						ng-model="litbomb.date"
						close-text="Close"
						placeholder="Enter date..." />
					<br />
					<button ng-click="dropBomb()">DROP!</button>

					<br /><br /><br />
					<b>Send Post Cards:</b>
					<br /><button ng-click="sendPostcards()">SEND!</button>
				</div>

			</div>
		</script>

		<!-- PERSON EDITOR MODAL -->
		<script type="text/ng-template" id="modal_personAdder">
			<div class="modalFrame clearfix">
				<h2>{{$root.mode}} Person
					<span ng-if="$root.mode == 'Edit'">- <a ng-click="goBack()">Back</a></span>:</h2>

				<div class="col-md-6">
					<label>First Name</label>
					<input ng-model="person.firstname" />
				</div>
				<div class="col-md-6">
					<label>Last Name</label>
					<input ng-model="person.lastname" />
				</div>
				<div class="col-md-12">
					<label>Bio</label>
					<textarea ng-model="person.bio"></textarea>
				</div>

				<div class="col-md-6">
					<label>Street Number</label>
					<input ng-model="person.stnum" />
					<br />
					<label>Street Name</label>
					<input ng-model="person.stname" />
					<br />
					<label>Unit</label>
					<input ng-model="person.unit" />
					<br />
					<label>City</label>
					<input ng-model="person.city" />
					<br />
					<label>State</label>
					<input ng-model="person.state" />
					<br />
					<label>Zip</label>
					<input ng-model="person.zip" />
				</div>
				<div class="col-md-6">
					<label>Support Level</label>
					<select ng-model="person.support_level">
						<option value="0">0 - Unidentified</option>
						<option value="1">1 - With Us</option>
						<option value="2">2 - Undecided</option>
						<option value="3">3 - Against Us</option>
					</select>
					<br />
					<label>Party</label>
					<select ng-model="person.enroll">
						<option value="U">Un-enrolled \ Unknown</option>
						<option value="G">Green</option>
						<option value="D">Democrat</option>
						<option value="R">Republican</option>
					</select>
					<br />
					<label>Year of Birth</label>
					<input ng-model="person.yob" />
					<br />
					<label>Email</label>
					<input ng-model="person.email" />
					<br />
					<label>Phone</label>
					<input ng-model="person.phone" />
					<br />
					<label>Profession</label>
					<input ng-model="person.profession" />
					<br />
					<label>Employer</label>
					<input ng-model="person.employer" />

					<br /><br />
					<button ng-click="savePerson()" class="btn btn-primary">SAVE!</button>
					<button ng-click="savePerson(1)" class="btn btn-primary">SAVE AND CLOSE!</button>
				</div>
			</div>
		</script>

	</div>


</div>

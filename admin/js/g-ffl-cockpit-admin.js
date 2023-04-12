(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	//jQuery('#ffl_list_background_option').iris();
	//jQuery('#ffl_list_text_option').iris();
	// Get the element with id="defaultOpen" and click on it        
	$(document).ready(function(){
		if (document.getElementById("defaultOpen") != null){
			document.getElementById("defaultOpen").click();
		}
	});

})( jQuery );


function setConfig(g_ffl_cockpit_key){
	// check if there are no errors
	if (editor.lastSchemaErrors.length==0){
		var config_json = editor.getText();
		document.getElementById('g_ffl_cockpit_configuration').value = config_json; 
		// submit to FFL API
		fetch("https://ffl-api.garidium.com/", {
			method: "POST",
			headers: {
			"Accept": "application/json",
			"Content-Type": "application/json",
			"x-api-key": g_ffl_cockpit_key,
			},
			body: JSON.stringify({"action": "update_subscription", "data": {"api_key": g_ffl_cockpit_key, "updates": {"cockpit_configuration": config_json}}})
		})
		.then((response) => response.json())
		.then((data) => {
			console.log("g-FFL Cockpit Configuration Changes:", data.success);
		})
		return true;
	}else{
		alert("Configuration Errors need to be resolved before saving.")
	}

	return false;
}

function openTab(evt, tabName) {
	// Declare all variables
	var i, tabcontent, tablinks;
  
	// Get all elements with class="tabcontent" and hide them
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
	  tabcontent[i].style.display = "none";
	}
  
	// Get all elements with class="tablinks" and remove the class "active"
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
	  tablinks[i].className = tablinks[i].className.replace(" active", "");
	}
  
	// Show the current tab, and add an "active" class to the button that opened the tab
	document.getElementById(tabName).style.display = "block";
	evt.currentTarget.className += " active";
}



=== g-FFL Cockpit ===
Contributors: garidium
Tags: FFL, eCommerce checkout, WooCommerce, FFL gun dealers, map api, google maps, gun dealer
Donate link: https://garidium.com/product/g-ffl-api-key/
Requires at least: 5.0
Tested up to: 6.1.1
Requires PHP: 7.0
Stable tag: 1.3.8
License: GPL v3+
License URI: https://www.gnu.org/licenses/gpl-3.0.html

==Description==
==Built by a FFL, for FFL's. This plugin will help streamline the firearm purchasing and fulfillment processes.==
It was built to support my business, and I know it will help yours as well. See the plugin in-action on [garidium.com](https://garidium.com).

==Feature Highlights:==
1. Easy to use FFL searching by zip, distance and name, showing a list view in the checkout page.
2. Optional Map view available, you can turn the map on and off in the plugin configuration. No Google Maps Key Required!
3. One-click ATF ezCheck page load in Order Details view.
4. Secure, crowd-sourced eFile system to upload and store FFL documents, making them available for FFL's to download during order fulfillment.

== API Subscription Required ==
While the plugin is free to download and install, it does require you to purchase a license for a personalized API key. The plugin will not be useful without this key. The API key allows the plugin to connect to the required data services, providing the following features:

1. Access to a database we manage that synchronizes weekly to the ATF FFL database
2. Access to the eFile system to download and upload FFL documentation
3. Access to Mapping and Geocoding features for displaying FFL locations on a map

== To purchase a key, visit the g-FFL API product page: ==
[g-FFL Product Page](https://garidium.com/g-ffl-api/) 

==Please review our Terms and Conditions: ==
[Terms and Conditions](https://garidium.com/terms_of_sale/)


== Installation ==
1. Search for the plugin in the marketplace
2. Click on "Install Now"
3. Click on "Activate"
4. Click on the “g-FFL Cockpit” link in the admin panel
5. Fill in the following information:
    - g-FFL API Key
    - Set Checkout Message (change email in the message)
    - If you want to offer in-store pickup, put your FFL number in the "In-Store Pickup FFL#" field, otherwise leave it blank. This field requires the full FFL number with dashes. 
    - Select the plugin position on the checkout page
    - Set your map on/off preference
6. Click on “Save Changes”


== Frequently Asked Questions ==
=Why is this plugin better than the alternatives?=
* It was built by a FFL, actively used by that FFL. I know what the industry needs, especially those first starting out with smaller budgets.
* The data services are built on state-of-the-art cloud-based technologies hosted on Amazon Web Services. This basically means that downtime will be minimal, and performance will be consistent.
* The plugin doesn't require a Google Maps API key, which adds additional cost and complexity to the installation process of other plugins
* We maintain a database of signed FFL copies for dealers that perform transfers. While its still your responsibility to ensure that the firearms your mailing go to a willing and able transfer FFL, having these FFL's on file and available for download is a big time saver in the fulfillment process
* The Order Details page provides you the ability to Upload FFL's and also quickly check the ATF ezCheck site for the FFL the customer selected during checkout
=How do I flag a product as FFL required?=
* The plugin provides two tools to do this, first you can check the "Requires FFL Shipment" checkbox on each individual product. This is located in the product data section. Secondly, on the main products list you can check 1-to-many products and use the bulk actions to set them all as FFL required products. In addition to these features, there is also a way to programmatically set products as FFL required. If you have questions about this option, please reach out to us.


== Screenshots ==
1. Here is what the plugin looks like on the checkout page. You will have the option to place the component in different locations. After the user enters in a zip code, a distance to search in miles, and an optional Name search, a list and map appear. The list will show FFL's in the zip code provided, along with any nearby zip codes within the distance selected. There is an indicator in the list as to whether or not we have the signed copy of the FFL on file. If it's green, we do... otherwise it's red. The customer will then select a FFL from either the list or the map, and the shipping details will be auto-populated. The shipping address fields non-editable, except by selecting a FFL via the plugin component.
2. Using the top-right control in the map, you can go into a full screen map mode. You can hit the escape key or click on the same icon to revert back.
3. Once a customer has placed an order, in the order details view we add two buttons, one for downloading the FFL if it;s on file, and the 2nd to Launch the ATF's ezCheck site to validate the FFL selected. These items are added in a new FFL Information section of the order page. 
4. If the customer selected a FFL where there is no signed FFL on-file, we allow you to upload that copy once you receive it. This will help streamline the process for the next time someone asks to ship to this FFL. 
5. The FFL ezCheck Button is a quick way to load ezCheck for the FFL selected by the customer, one-click.  
6. The Download FFL will securely retrieve the FFL copy on-file. These links are not shareable and work for you as a subscriber to the g-FFL API

== Changelog ==
15. v1.3.8 Minor fix for situation when user changes FFL during checkout 
14. v1.3.7 Fix display of Tax and Shipping Costs after FFL selection 
13. v1.3.6 Improved load speed of orders page, checking for on-file FFL 
12. v1.3.5 Max Zoom issue resolved on Local Pickup option
11. v1.3.4 FFL-Download button now hitting API to get real-time status
10. v1.3.3 Added progress indicator to load panel
9. v1.3.2 Added local pickup option, which is configurable on the admin page
8. v1.2.5 Unauthorized message on checkout if plugin is not approved, this previously would be a silent alert
7. v1.2.4 Preload API when no map is selected to improve performance of FFL search
6. v1.2.3 Page jumps to first and last name after seleecting FFL when map is disabled and plugin above shipping
5. v1.2.2 Map is now optional, FFL Information now in custom fields instead of notes, and shipping forms are removed if FFL-required purchase
4. v1.1.3 Resolved an issue with caching shipping address from FFL purchase to non-FFL purchase
3. v1.1.2 Mapbox conversion, removing need for Google Maps API key and also adding eFile FFL upload/download/ezCheck features to orders details view
2. v1.0.1 Bug Fixes
1. v1.0.0 Initial Release of the g-FFL API & Plugin
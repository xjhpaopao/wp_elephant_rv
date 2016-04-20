=== WooCommerce Checkout Manager ===

Contributors: visser, Emark
Tags: woocommerce, ecommerce, e-commerce, store, cart, checkout, manager, editor, field, shipping, billing, order
Requires at least: 3.0
Tested up to: 4.5
Stable tag: 4.0.2
License: GPLv2 or later

Manages WooCommerce Checkout, the advanced way.

== Description ==

This extension plugin gives you capabilities to manage your fields on your [WooCommerce](http://wordpress.org/plugins/woocommerce/) Checkout page.

**Notice**: There has been a change of Plugin ownership on 11/03/2016, please see the *Change of Plugin ownership* section below for more information.

= FEATURES =

* Add new fields to the checkout page and re-order them.
* Make checkout fields optional.
* Remove & Make required fields. 
* Added fields will appear on Order Summary, Receipt and Back-end in Orders.
* Enable/ Disable "Additional Fields" section name on the Order Summary and Receipt.
* **Four ( 4 )** fields types included: Text Input + Check Box + Select Options + Date Picker.
* Compatible with [WPML](http://wpml.org/) | [WooCommerce Print Invoice & Delivery Note](http://wordpress.org/plugins/woocommerce-delivery-notes/) | [ WooCommerce Order/Customer CSV Export](http://www.woothemes.com/products/ordercustomer-csv-export/)
* Show or Hide fields for user roles
* Upload files on Checkout Page
* Sort Orders by Field Name
* Export Orders by Field Name
* Add new fields to the **Billing** and **Shipping** Section **separately** from Additional Section. 
* These fields can be edited on your customers **account** page.
* **Fifteen ( 16 )** field types included: Text Area + Password + Radio + Select + Pre-defined Check Box + Time Picker + **Text/ Html Swapper** + Color Picker + Heading + Multi-Select + Multi-Checkbox + **File Picker** etc...
* Create Conditional Fields.
* Create field to remove tax.
* Create field to add additional amount.
* Replace Text using Text/ Html Swapper.
* Allow Customers to **Upload files** for each order on order details page.
* Show or Hide added field for Specific Product or Category Only.
* Display **Payment Method** and Shipping Method used by customer.
* Disable any added field from Checkout details page and Order Receipt.
* **Retain fields information** for customers when they navigate back and forth from checkout.
* Disable Billing Address fields for chosen shipping goods. Which makes them visible only for virtual goods.
* **DatePicker:** Change the default format (dd-mm-yy), Set Minimum Date and Maximum Date, Disable days in the week (Sun - Sat).
* **TimePicker:** Includes restriction of both start and end hours, set the minutes interval and manually input labels.
* Area to insert your own **Custom CSS**.
* Display **Order Time**.
* Set Default State for checkout.
* **Import/ Export** added fields data.
* Fields label can accept html characters.
* Re-position the added fields: Before Shipping Form, After Shipping Form, Before Billing Form, After Billing Form or After Order Notes
* **Insert Notice:** Before Customer Address Fields and Before Order Summary on checkout page.

= Change of Plugin ownership =

11/03/2016 marks a change of ownership of WooCommerce Checkout Manager from Emark to visser who will be responsible for resolving critical Plugin issues and ensuring the Plugin meets WordPress security and coding standards in the form of regular Plugin updates.

== Installation ==

= Minimum Requirements =

* WooCommerce 2.2 +
* WordPress 3.8 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= WP installation =

1. Log in to your WordPress dashboard
2. Navigate to the Plugins menu and click Add New.
2. Click Upload Plugin
3. Click Choose File and select downloaded zip file
4. Click Install Now

= NP: The downloaded zip file is the file that you download from wordpress.org. =

= FTP installation =

The manual installation method involves downloading the plugin and uploading it to your webserver via an FTP application. The WordPress codex contains instructions on how to do this here.

= Updating =

Automatic updates are delivered just like any other WordPress plugin.

== Frequently Asked Questions ==

= How to fix fields that are not showing on checkout page properly? = 
Usually this is an CSS issue. If your theme comes with the option to input your very own custom CSS, you can use the abbreviation field name as part of the CSS code to set the fields in the way that you want. 

Example :
`#myfield1_field {
	float: right;
}`

= How do I review the data from the custom fields? =

Your order data can be reviewed in each order. By default your "Custom Fields" section should be showing allowing you to see the custom fields data.
If the fields are not showing, follow these steps:

1. Go to your desired Order.
2. Click "Screen Options"
3. Check "Custom Fields"
4. Scroll down till you see "Custom Fields" section.

= How do you access saved data to be used with WooCommerce PDF Invoices & Packing Slips? =

The above plugin requests that you code the fields in the template. To access the saved data, use the abbreviation name of the field. As we are using the first abbreviation field as an example. Where "myfield1" is the abbreviation name, and "My custom field:" is the label.

Example:
`<?php $wpo_wcpdf->custom_field('myfield1', 'My custom field:'); ?>`

== Screenshots ==

1. SETTINGS PAGE

2. ORDER SUMMARY

3. RECEIPT

4. INPUT TYPE

5. CHECKBOX

6. DATE PICKER

7. SELECT OPTIONS

== Changelog ==

= 4.0.2 =
* Fixed: PHP warning notices on Checkout page (thanks sfowles)
* Fixed: PHP warning on Export screen
* Changed: Cleaned up the Import dialog
* Fixed: jQuery error on Billing file upload field
* Fixed: Add Order Files on Edit Order screen uploader
* Fixed: References to hard coded Plugin directory
* Fixed: References to hard coded Pro Plugin directory

= 4.0.1 =
* Changed: Change of Plugin ownership from Emark to visser
* Changed: Removed registration key engine
* Fixed: WooCheckout Admin menu entries
* Fixed: PHP warning on WooCheckout screen
* Changed: Data update required notice for 4.0+ upgrade
* Added: Modal prompt on data update notice
* Changed: Heading placement on Setting and Export screen
* Changed: Order of Sections on Export screen
* Added: Modal prompt on reset button
* Fixed: Sanitize all $_GET and $_POST data

= 4.0 =
* Validation Error Fixed.
* Fix minor security issues
* Export Options fixed
* Minor data display fixed
* User roles bug fix.
* Restrict display of fields by user roles.
* Restriction added - File Types, Max number of Uploads, Upload for order status
* Hidden toggler and Conditional conflict fixed.
* Offset fixed.
* File Upload bug fixed.
* Color Picker Update
* File Picker added
* Field filter fixes
* Checkbox fixes.
* Storage fixes.
* Checkbox Toggler deprecated - Use Option Toggler for checkbox vlaues
* Class function added.
* Checkbox & Conditional in both Billing and Shipping Fixed.
* License GUI fix.
* Conditional Biling fix #1.
* Required fix shipping #1
* Retain fields fix 1.
* GUI upgrade.
* Conditional required fix.
* important update! - Required fix 3.
* Remove duplicates in shipping column.
* important update! - Required fix 2.
* Required fields, revert back.
* Billing, Shipping Required fix.
* Hide field from product, fix.
* Reset option fix.
* Major Updates fix2.
* Major Updates fix.
* Sort by Field Name
* GUI fix.
* Copy suffix, fix.
* Included sort feature.
* Extra Export feature included.
* WooCommerce built in export compatible.
* Export fix.
* Radio button name changed.
* Session limiter on cart page fixed.
* Tax remove fixed.
* Retain fields fixed.
* Add amount fixed.
* Select options translation fixed.
* Order Details page fix 1.
* Required fields fix 1.
* Fields Display on e-mail.
* Translation in notices fixed.
* Backend fields display fixed.
* Create field limit fixed.
* Text/ Html Swapper fix.
* Fields disappears on update, fixed.
* Javascript error fixed.
* 7 field creation expanded and fixed.
* Export functions fixed.
* Upgrade notice fix.
* Minor bug fix.
* Fixes empty array errors.
* Make all fields required. 
* Minor bug fixes.
* Add new fields to the billing fields.
* Add new fields to the shipping fields.
* Fields show in Account Page.
* Select Options fixed + Required fields fixed.
* Compatible with WP 4.1
* Update of debug mode errors
* Errors fixed for debug mode.
* Fee function fixed.
* Upload bug fix. License check fix.
* Hide field bug fix.
* Multi-Checkbox included.
* Bug fix for uploading files back-end.
* Positioning + Clear added for billing and shipping section.
* Minor bug fixes.
* Datepicker languages added.
* Admin language switch added.
* WPML bug fixed.
* Bug fix in Show & Hide Field Function
* More function added for hiding of fields
* Conditional Bug fix.
* Compatibility with 2.1.7 WooCommerce && WPML
* Checkout compatibility
* minor bug fix.
* Minor bug fixes, GUI upgrade.
* Two new field types included. 
* Import/ Export added fields data.
* Fields label can accept html characters. 
* Unlimited Select Options and Radio Buttons
* Bug Fix: Automatic update fix & DatePicker
* Bug Fix: Conditional Logic

= 3.6.8 =
Add Error Fix 2.
GUI upgrade.

= 3.6.7 =
Add Error Fix.
Add WooCommerce Order/Customer CSV Export support
Able to Change additional information header

= 3.6.6 =
GUI + Code clean up.
Multi-lang Save issue fix.

= 3.6.5 =
WPML bug fixes 4

= 3.6.4 =
WPML bug fixes 3 

= 3.6.3 =
WPML bug fix 2 (translation for e-mails)

= 3.6.2 =
WPML bug fix

= 3.6.1 =
Compatibility with 2.1.7 WooCommerce && WPML

= 3.6 =
Bug fixes.

= 3.5.9 =
Bug fix.

= 3.5.81 =
Bulgarian language by Ivo Minchev

= 3.5.8 =
Bug fix.

= 3.5.7 =
Bug fix.

= 3.5.6 =
Included translations - Vietnamse, Italian, European Portuguese, Brazilian Portuguese
Layout fixed on Order Summary Page

= 3.5.5 =
Translations updated

= 3.5.4 =
Added feature.

= 3.5.3 =
bug fix- force selection for option and minor fix.

= 3.5.2 =
updating to standard.

= 3.5.1 =
Select option and checkbox functions, included.

= 3.5 =
Select date function, included.

= 3.4 =
bug fixed.

= 3.3 =
fields positioning, fixed.

= 3.2 =
code review

= 3.1 =
bug fix

= 3.0 =
Javascript fix and rename fields inserted

= 2.9 =
Bug fixes

= 2.8 =
Bug fixes

= 2.7 =
required attribute bug fix and included translations

= 2.6 =
remove fields for shipping

= 2.5 =
Added features for shipping

= 2.4 =
Localization Ready

= 2.3 =
Additional features

= 2.2 =
bug fix

= 2.1 =
Checkout process fix

= 2.0 =
Custom fields data are added to the receipt

= 1.7 =
add/remove required field for each new fields

= 1.6 =
more bugs fixed

= 1.5 =
some bugs fixed

= 1.4 =
More features added.

= 1.3 =
bug fix!

= 1.2 =
Added required attribute removal

= 1.0 =
Initial

== Upgrade Notice ==

= 2.0.1 =
The 2.0.1 Plugin update marks a change of ownership of WooCommerce Checkout Manager from Emark to visser who will be responsible for resolving critical issues and ensuring the Plugin meets WordPress security and coding standards in the form of regular Plugin updates.
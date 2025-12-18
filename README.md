## Vehicle Booking System Plugin for Wordpress

Vehicle Booking System is a flexible WordPress plugin designed for taxi companies and Uber-style transportation services. It enables customers to book vehicles online with ease, while providing operators with a streamlined system to manage rides, schedules, pricing and more.

## Features

#### Bookings

Bookings are where the business is. You can see a detailed list of all boookings and make changes as needed to each booking. You can also see a map of the route as well as the cost breakdown.

#### Vehicles

Add as many vehicles as you need. Each vehicle has it's own pricing structure (Flat or Incremental) as well as it's own capacity settings. You can also set the vehicle category and fuel type for informational purposes.

Flat pricing gives you a single cost per km of distance while Incremental allows you to set distance ranges with different costs. For example, €0,1/km for the first 50km, €0,14 for the next 90km etc

#### Drivers

Keep all your driver information in one place. Each driver has a set of basic contact and licensing information. Drivers can be assigned to bookings as long as they are active.

#### Locations

Locations allows you to have a list of pre-defined locations the customer can choose from while booking a ride. This makes it easier for them since they don't have to type a specific address. Customers can switch between a location or a typed address in the booking form for pickup or dropoff location.

#### Addons

Addons are additional services or equipment the user can add to their chosen vehicle. Addons may include things like child seats, a towable trailer etc. Each addon has a cost and can be assigned to any number of vehicles.

#### Surcharges

Surcharges are additional costs related to a location or date range. For example, you can add a surcharge when the customer books a ride during Christmas or when they choose to be picked up or dropped of in a specific location.

#### Payment methods

Currently the plugin supports Cash payments, Stripe and PayPal. More to be added in the future.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Install required plugins as needed
1. Go to the plugin settings to adjust basic settings
1. Add vehicles, adjusting their pricing structure
1. Add Addons for vehicles as needed
1. Add Surcharges as needed
1. Add pre-defined locations for users to choose from

## Shortcodes

These are the plugin's shortcodes, in the order they are used during the booking workflow:

 - `[vbs_booking_form]` Displays the booking form the customers use to book rides
 - `[vbs_vehicles_list]` Displays the list of all available vehicles based on the form inputs
 - `[vbs_addons_list]` Displays the list of available addons for the selected vehicle
 - `[vbs_customer_information]` Displays the customer information form
 - `[vbs_booking_summary]` Displays thr booking summary, as well as the available payment methods
 - `[vbs_payment_pending]` For stripe payments requiring a redirection, this needs to be in the page the users get redirected to
 - `[vbs_booking_confirmation]` Displays the confirmation of the booking after payment

## Frequently Asked Questions

#### Do you plan on switching to blocks instead of shortcodes?

Yes, at some point in the future. I want to keep things simple and as compatible as I can for now.

## Changelog

#### 1.0
* Initial Release
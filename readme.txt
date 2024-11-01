=== Stripe Basic ===
Contributors: wptemplatesstore
Tags: stripe, stripe subscription, stripe basic, stripe buy button, stripe gateway
Requires at least: 4.9.0
Tested up to: 4.9.7
Requires PHP: 5.0
Stable tag: 1.5.0

Quickly add Stripe buy buttons and recurring payment buttons anywhere on your site.

== Description ==
This basic version lets you add as many Stripe payment buttons to your Wordpress site as you need. From simple one-off payments to recurring subscription based payments.

<strong>Must haves</strong>

Firstly, you'll need a Stripe account. You'll also need your website to have an SSL certificate for Stripe to process payments securely.

<strong>How it works</strong>

Go to the new Stripe option in your admin menu, fill in the publish and secret keys, then start creating buttons.

Select the Add New option and fill in all the required fields. Save it. Now look at the Stripe Buttons menu item. Here you'll get the shortcodes you need. The first is the button. Add this where you want your button to show. Add the success and fail shortcodes to the pages you selected.

That's it. You are now taking payments through your site.

<strong>Features</strong>

<ul>
<li>Easy setup</li>
<li>One-off payments</li>
<li>Recurring payments</lI>
<li>Error handling</lI>
<li>Shortcodes can be placed anywhere on the site</li>
<li>Add CSS classes to your buttons to match your site</li>
</ul>

<strong>Additional</strong>

Nothing is perfect. So there is an Error menu item where you can see any failed transactions that may occur. This will give you a description of the button that was used, and the customer email address so you can easily follow them up.

== Installation ==

<h4>Using The WordPress Dashboard</h4>
<ol>
<li>Navigate to Add New from your dashboard</li>
<li>Search for Stripe Basic</li>
<li>Click Install Now</li>
<li>Activate the plugin</li>
</ol>
<h4>Uploading via WordPress Dashboard</h4>
<ol>
<li>Navigate to the Add New in the plugins dashboard</li>
<li>Navigate to the Upload area</li>
<li>Select <code>stripe-basic.zip</code> from your computer</li>
<li>Click Install Now</li>
<li>Activate the plugin in the Plugin dashboard</li>
</ol>
<h4>Using FTP</h4>
<ol>
<li>Download <code>stripe-basic.zip</code></li>
<li>Extract the <code>stripe-basic</code> directory on your computer</li>
<li>Upload the <code>stripe-basic</code> directory to the <code>/wp-content/plugins/</code> directory</li>
<li>Activate it from the Plugins dashboard</li>
</ol>

== Frequently Asked Questions ==
<b>Can I test the buttons before they go live?</b>

Yes. You can set "Test Mode" to on which will let you see the functionality in action before going live.

<b>What card details should I use when testing?</b>

Use card number 4242 4242 4242 4242 with any other legit details to test the sales process. These successful sales will appear in your test dashboard.

== Screenshots ==
1. General settings
2. Add new
3. Stripe popup

== Changelog ==
Version 1.0

Launch

Version 1.2

Success and Fail URLs changed.

Version 1.3

Success and Fail URLs changed... again (bug fix)

NEW Feature added - Ability to add ?a=XXXX to the end of the button page URL to make editable oneoff payment buttons

Version 1.4

Bug fixes

Version 1.4.1

Bug fixes and extra error messages

Version 1.5.0

Update to allow for child success and fail pages
Added functionality to allow for billing cycle intervals

== Upgrade Notice ==
Upgrades keep the plugin working if Stripe ever changes the process in which payments are taken.

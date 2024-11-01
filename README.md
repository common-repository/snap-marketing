=== Snap Marketing ===
Contributors: snapfinance
Donate link: https://developer.snapfinance.com/
Tags: Finance, Money, Loan, Ecommerce, Short term Loan.
Requires at least: 4.5
Tested up to: 6.6.2
Stable tag: 1.0.9
Requires PHP: 7.0
License: GPLv2 or later
License URI - http -//www.gnu.org/licenses/gpl-2.0.html

== Description ==

Snap Finance is a lease-to-own financing provider that empowers shoppers to get what they need now, pay later.

Our Snap Marketing plugin can help drive conversions throughout a customer’s shopping journey. Show how much they could be approved for on your site and what their payments could look like using contextual top of funnel marketing placements. Reduce cart abandonment and increase average order value by giving your customers greater shopping power. 

* Out-of-the-box merchandising assets to drive top of funnel conversions.
* Snap approves amounts from $150 up to $5,000.
* Multiple ownership options, including 100-Day and early buyout options.

The Snap Marketing plugin enables preapproval functionality where your credit-challenged shoppers can get potentially approved during key moments of their shopping journey on your webstore; thereby, giving you greater ability to close more sales.  There are multiple ways to drive e-commerce preapproval customers, with our "Get Approved" and "Get Approved - As low as" treatments. We encourage you to use both on key places on your site to drive visibility for customers who may need financing to transact on your store.
Enable the "Get Approved" promotional treatment on any pages of your webstore.  Let customers know they have a financing option, from the start, to turn browsers into actual customers.  With the preapproval application flow, if approved, customers will know exactly how much they have been approved for, giving them the motivation to transact for a higher shopping cart value on your store.  As research shows, when customers know they have an approval and the amount, not only are they likely to purchase but purchase more items or a higher price point item. 

= Assets for Banners and logo =
Plugin uses Snapfinance server resources to load banners and logos used in treatment.

= Get Approved - As low as =
Offer contextual financing experience with "Get Approved - As low as" treatment and banners
Enable the "Get Approved - As low as" promotional treatment and get enhanced benefits of the "Get Approved" but customized for your product pages.  Place the treatment in your product pages to give customers a glimpse of what their payments may look like with Snap. Our research has shown that customers are more likely to apply and transact if they have an idea of how much their payments will look like with Snap.

== Installation ==

= Activate Plugin =
1.  Log in to your WooCommerce account and select the  **Plugins**  tab.
2.  Select  **Activate**  for the Snap Marketing plugin.
3.  Go to  **Snap Marketing > Configuration**.
    The Snap Marketing Configuration dialog box displays.
4.  Select  **Enable Snap Marketing**.
5.  Select the production or sandbox environment.
6.  Enter the corresponding client ID and secret key.
7.  Select  **Save Changes**.
**[ NOTE ]**  Per WooCommerce functionality, if you disable the plugin while you have treatments deployed on your pages,  _snap_treatment_  IDs display instead of Snap treatment names. Re-enable the plugin to display the treatment names. See example screenshot screenshot-6.png
Congratulations, your Snap Marketing plugin is now configured and communicating with Snap. Now let's make your first treatment.

= Manage Treatments =

* Add Treatment
1.  Log into your WooCommerce account and select  **Snap Marketing**  from the left menu.
    A list of current treatments displays.
2.  Select  **Add New**.
    The Add New Treatment page displays.
3.  Enter a treatment name for internal reference.
4.  Select a treatment type.
5.  From the Logo URL drop-down menu, select a Snap logo to display next to the text.
6.  From the Active? drop-down menu select an option to determine whether the treatment will be active on creation.
7.  Every treatment will have to be placed via the  [Wordpress Shortcode](https://codex.wordpress.org/Shortcode) methodology. The Shortcode for this treatment is at the bottom of the page.
8.  You can use the  **Alignment** drop-down menu to determine how the treatment is positioned in the DIV column on the page. (**Left, Center, Right**)
(For As-Low-As-Treatment only)
1.  There is a check-box labeled "**Enable in all product description pages?"** Checking this box will automatically show the As-Low-As treatment on all product description pages.

* Edit Treatment
Hover your cursor over the treatment name and select the  **Edit**  option that displays.

* Delete Treatment
Hover your cursor over the treatment name and select the  **Trash**  option that displays.


== Screenshots ==
* screenshot-1.png
As Low As treatment on product page
* screenshot-2.png
Beginning of Snap approval flow
* screenshot-3.png
Approval page
* screenshot-4.png
Snap Checkout payment page
* screenshot-5.png
Order review page


== Upgrade Notice ==

Initial release.

== Frequently Asked Questions ==

= How do I apply for a developer account? =
Snap developer program for now is open for existing merchants who wants to offer financing to their cusotmers at the time of checkout.
If you are an existing merchant please ask your sales reps to enable your account for ecommerce and then email devsupport@snapfinance.com with your account details and we will set up your developer account for testing in sandbox and send you the details. If you are not an existing merchant, please fill out this application https://snapfinance.com/partner to be onboarded as a merchant.

= How to get client id and client secret key ? =
You need to login or signup to https://developer.snapfinance.com/ to generate client id and secret key.

= How can Merchant check for Loan Application status ? =
Merchant has to login to https://merchant.snapfinance.com/ to learn loan application status.

## Changelog

## 1.0.0
Initial release.

## 1.0.1
Added functionality of email logs on installation/installation of plugin
Changed hooks to solve various conflicts 
Updated code to remove SDK conflict on checkout page 

## 1.0.2
AS LOW AS section should be hidden from UI if the product price is 0 or out of stock or no price is set
Text change in the AS LOW AS section

## 1.0.3
Updated the latest banners for Marketing treatment in admin side

## 1.0.4
Incorrect Stable Tag fix
Out of Date plugin version fix
Sanitized, Escaped, and Validated for request data fixes
Unsafe SQL calls fixes
Nonces and User Permissions Needed for Security fixes
Variables and options must be escaped fixes 

## 1.0.5
Updated plugin code for latest wordpress and woocommerce version.

## 1.0.6
Update snap logo

## 1.0.7
Update snap logo

## 1.0.8
Updated ALA and Get Started content/verbiage

## 1.0.9
Updated Marketplace content and add training url
## Installation

It's regular Wordpress plugin, so just download in ZIP archive or download files then make a ZIP archive in your machine, upload it into Wordpress as standard plugin, activate and you're ready to go. You can find 'Woo Ameria vPOS Payment' payment method in WooCommerce settings page, under Payments tab.

## Setup

In setup/options page of plugin, within WooCommerce Payments tab, there are 4 fields - username, password, client id and back uri. You'll get the first 3 from Ameria after completing registration/contract sign-up process for providing services of virtual POS system. back uri is the absolute path for your callback, where browser will be redirected after successful/failed completion of payment within Amerias' website.

Create that callback file before using this plugin as real payment option in your store. Ameria will send following data in ```POST``` request: orderID, respcode, paymentid and opaque. '00' in respcode means successful completion of payment. orderID is id of order in your store for which payment is made - use it to set appropriate status for that order and make other relevant updates. paymentid is used to display check of payment within Amerias' website. Uri looks like this - ```https://payments.ameriabank.am/forms/frm_checkprint.aspx?paymentid=``` + ```paymentid```

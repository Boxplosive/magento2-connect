# Boxplosive Plugin for Magento® 2

The official extension to connect Boxplosive with your Magento® 2 store and start with saving points and promotions.

## Install via Composer


1. Go to Magento® 2 root folder

2. Enter the following commands to install module:

   ```
   composer require boxplosive/magento2-connect
   ``` 

3. Enter the following commands to enable module:

   ```
   php bin/magento module:enable Boxplosive_Connect
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   ```

4. If Magento® is running in production mode, deploy static content with the following command: 

   ```
   php bin/magento setup:static-content:deploy
   ```

## Install from GitHub

1. Download zip package by clicking "Clone or Download" and select "Download ZIP" from the dropdown.

2. Create an app/code/Boxplosive/Connect directory in your Magento® 2 root folder.

3. Extract the contents from the "magento2-Boxplosive-master" zip and copy or upload everything to app/code/Boxplosive/Connect

4. Run the following commands from the Magento® 2 root folder to install and enable the module:

   ```
   php bin/magento module:enable Boxplosive_Connect
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   ```

5. If Magento® is running in production mode, deploy static content with the following command: 

   ```
   php bin/magento setup:static-content:deploy
   ```
   
## Development by Magmodules

We are a Dutch Magento and Shopware Plugin development firm. Located in The Netherlands we develop plugins to connect any systems with Magento and Shopware.
[Visit Magmodules.eu](https://www.magmodules.eu/)

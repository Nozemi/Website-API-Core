# Nozemi's API Core

This version is a customization towards Vestfold Chiptuning.


#### Requirements
The foillowing requirements are needed in order to run this API Core. If not met, you won't be able to efficiently run this API.

- Composer
- PHP 7.0 (or higher)

#### Installation
1. Run `composer install `
2. You're ready to go, and it should be able to run out of the box.


#### Things to consider
- When a customer puts something in their shopping cart. It's possible that the product(s) can be increased of decreased in price. We need to figure out how to handle this. Possible solution would be to save the curent order once they proceed to the payment page, where they'll be promted with a message telling them that product X,Y,Z have changed in price since they were added to the cart, and asking if they still want to keep them in the cart.
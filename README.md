# CDP Voters
Files used to create the voter validation page for the Community Democracy Project, built by Kyle / Cooperative 4 the Community. You can search for people in the Oakland voter database and mark them valid, or mark invalid if you can't find them. Supports partial searches.

## Environment
PHP: 7.3.14
WordPress: 5.2.5
WordPress plugin Code Embed: 2.3.2
Firebase: 7.4.0, free account, enable web app

Purchase a voter database from your local government. Do some validation; there were several issues in the Alameda County / Oakland database.

## Installation
Set up your firebase project with a database and require Google email authentication.
Import the purchased database into your web host's MySQL database.
Add cdp-voters to your server's wp-content/plugin directory and activate it from WordPress plugins.
Copy/Paste the top of the html file into your WordPress page using Code Editor.
Copy/Paste the javascript functions into the Code Embed area in your WordPress page.

## Screenshots
A single result when searching the database with a specific address. If the name matches you can mark it valid, or mark invalid if it's the wrong person.

![Image of address validation result](https://cooperative4thecommunity.com/wp-content/uploads/2020/02/validation_result.png)

Multiple results when searching for a specific first name with pattern matching in the street name.

![Image of pattern validation result](https://cooperative4thecommunity.com/wp-content/uploads/2020/02/partial_search.png)

## Acknowledgements
Neil and Victoria for doing all the paperwork to buy the Oakland voter database.

Tia for user-testing and finding all the little edge cases to cover.

Fred and Liz for diligently validating signatures over many weeks.

Aliya, Chris, and Nick for staying up late validating signatures with me.

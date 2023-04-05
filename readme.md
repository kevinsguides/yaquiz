# YAQ (Yet Another Quiz Extension)
## For Joomla 4
Developer: [KevinsGuides.com](https://kevinsguides.com) / Kevin Olson
**License GPL v3**
[Full Documentation / How To Use](https://kevinsguides.com/guides/webdev/joomla4/free-extensions/yaq)


This is a component for Joomla 4.

It allows you to add graded review quizzes to your website.

Currently, this is in beta. Do not use on critical sites or without testing.
## Requirements
You just need a site running Joomla 4 with a template that uses Bootstrap 5 (most templates will work). Install it like any other component. Extension works with Joomla's cache system, but not the full page cache plugin.


## What's Working...
* Create Quizzes and Questions
* Multiple Choice Questions
* True/False Questions
* Fill in blank / input text Questions
* Question Points System
* Basic Question Feedback System
* Basic form validation
* Quiz access levels on front end
* Some, but not all permissions are respected
* Question ordering
* Single page or multi page quizzes
* Basic access control (for create edit delete)
* record hit and submission count of each quiz
* checkins for quizzes are working
* javascript quiz mode
* cache first page of quizzes (or entire quiz for js quizzes)
* Able to save general and individual quiz results for every user
* Display general stats about a quiz, like # of subs and average score
* Import questions from Excel spreadsheet 
* Basic templating system for all front end pages

## Todo

* Limit number of attempts option
* New permission for viewing other users results
* cool circle results graph or something else
* checkins for questions
* reset hit/sub count button
* quiz by category - a quiz that automatically pulls all questions from a category
* randomize from pool - ability to select a random # of questions from a category
* Other question types
    * hot spot
    * matching
    * dropdown fill in blank
* Clean/consistent interface
* Fix Language Files (~70 done)
* Additional styles

* Quiz Certificates



### Basic Build Instructions
You can build from source by creating a component install package manually.
1. Create new directory.
2. Create "admin" and "site" directories
3. Copy contents of components/com_yaquiz into site
4. Copy contents of administrator/components/com_yaquiz into admin
5. Move yaquiz.xml file from admin to root of new directory
6. Zip everything and install as normal

If you want excel function to work you need to run composer install/update commands and ensure all dependencies are in components admin/vendor folder
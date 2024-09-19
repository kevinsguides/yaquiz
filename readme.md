# YAQ (Yet Another Quiz Extension)

#PROJECT DISCONTINUED 9/19/24
I no longer have time to support this and frankly it's too large of a project to be worth the $100 I've made off it. Ya cheap batards lol.
I'll leave it up if anyone wants to pick it up.
- Kevin



## For Joomla 4/5
Developer: [KevinsGuides.com](https://kevinsguides.com) / Kevin Olson
**License GPL v3**
[Full Documentation / How To Use](https://kevinsguides.com/guides/webdev/joomla4/free-extensions/yaq)


This is a component for Joomla 4/5.

It allows you to add graded review quizzes to your website.

Currently, this is in beta. Do not use on critical sites or without testing.
## Requirements
You just need a site running Joomla 5 with a template that uses Bootstrap 5 (most templates will work).

Install it like any other component. Extension works with Joomla's cache system, but not the full page cache plugin.


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
* You can set a limit on the number of quiz attempts for logged in users.
* Permission for viewing other users results
* Page to view users results in backend
* reset stats, attempt count, etc.
* two main styles + custom
* Quiz Certificates (PDF)
* Router with SEF URLs, Quiz item alias, etc.

## Todo
* parent front end all quizzes view
* HTML quiz cert option for performance or compatibility
* expire/auto remove results after x days
* maybe show all quizzes the user has taken on first page, then their results for each quiz on a separate page, rather than all results for all quiz on one page
* make checkin system work for quizzes and questions
* quiz by category - a quiz that automatically pulls all questions from a category
* randomize from pool - ability to select a random # of questions from a category
* Other question types
    * hot spot
    * matching
    * dropdown or dragging fill in blank
* Fix Language Files/Constants (~90 done)
* Individually edit student grades, delete individual records, etc.
* Score/range based quizzes (Personality tests, diagnostic tests, range etc.)
* Email results to teacher and/or student (or just use kind of like a general form builder)
* Auto category mode - Each quiz is given a unique category and questions are automatically isolated to that quiz
* Easy insert mode - add quiz questions with JS, more responsive
* Overhaul JS quiz system to tie in with rest of the system



### Basic Build Instructions
You can build from source by creating a component install package manually.
1. Create new directory.
2. Create "admin" and "site" directories
3. Copy contents of components/com_yaquiz into site
4. Copy contents of administrator/components/com_yaquiz into admin
5. Move yaquiz.xml file from admin to root of new directory
6. Zip everything and install as normal

If you want excel function to work you need to run composer install/update commands and ensure all dependencies are in components admin/vendor folder
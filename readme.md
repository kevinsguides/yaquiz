# YAQ (Yet Another Quiz Extension)
## For Joomla 4
Developer: [KevinsGuides.com](https://kevinsguides.com) / Kevin Olson
**License GPL v3**


This is a component for Joomla 4.

It allows you to add graded review quizzes to your website.

Currently, this is in beta. Do not use on critical sites or without testing.
## Requirements
You just need a site running Joomla 4 with a template that uses Bootstrap 5 (most templates will work). Install it like any other component. Extension works with Joomla's cache system, but not the full page cache plugin.

## How to Use
* Optionally define a category to put related quizzes and questions in. You can do 1 category per quiz, or make it more general.
  * If you have very few quizzes/questions you can probably get away with not using any categories
  * Categories have no bearing on quiz operations (yet) - categories only filter quizzes/questions on the back end
* Create your questions using the question editor - questions can be multiple choice, short answer, or true/false
  * You can add any HTML, including images, lists, etc. in the details section of the question
  * You can add Math equations using LaTex or MathML with the math plugin enabled (see global config settings for the component)
  * You can assign point values to questions if you'd like. You must turn on the point system once you configure the quiz. Otherwise, all questions count as 1 point each.
* Create the quiz to hold the questions with the quiz editor
  * Create and save the quiz, then go to the details/add questions page for that quiz
  * Use question insertion tool to find and add previously created questions to this quiz
* You may preview the quiz by clicking the links from the quiz editor.
* When quiz is ready, make sure it's access level is set to whatever is appropriate (default is public), and that the quiz is published.
* Link to the quiz with the link provided in the backend, or add the quiz as a menu item using the menu manager.
* Report any bugs to Kevin by reaching out on the website forums
* For priority support, please tip what you can (at kevinsguides.com) and send an email to the address provided once your payment is complete.
* I'll customize this extension for you.
  * basic styling from $30
  * more complex tasks from $100


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

## Todo

* record grade statistics option
* mass imports of questions
* limit available qns selection to like 50 and show addtl message
* cool circle results graph
* checkins for questions
* better templating system in general - easier extensibility....
  * base templates work but subfolders do not for overrides - see if require once can be made relative
* display grade statistics option
  * record average score, individual user scores, etc...
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
* Record user results and save to db permanently
* Quiz statistics/review results
* Quiz Certificates

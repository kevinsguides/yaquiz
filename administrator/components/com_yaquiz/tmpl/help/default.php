<?php

namespace KevinsGuides\Component\Yaquiz\Administrator\View\Help;

defined('_JEXEC') or die;

?>

<h1>Simple Quiz Help</h1>
<br/>
<a href="https://kevinsguides.com" target="_blank">By: KevinsGuides.com</a>
<p>Hello, thank you for using Simple Quiz!</p>
<p>Simple Quiz is a quiz component for Joomla. It's designed for creating quizzes and tests on your website.</p>
<h3>Categories</h3>
<p>Categories are used to help organize your quizzes <em>and</em> questions. Using them is optional.</p>
<p>There is only 1 set of categories, so quizzes and questions share the same categories.</p>
<p>You can create subcategories, but the component will only filter 1 category at a time (not a category AND its subcategories)</p>
<p>How you structure your categories is up to you. If you only have a few quizzes, you might not need any categories. If you have dozens or hundreds, you'll want a lot of categories.</p>
<h3>Getting Started...</h3>
<p>After installing the component, here is how to create a quiz with questions.</p>
<ol>
    <li>First, create a category for your quiz. It could be the subject of the quiz, the name of the quiz, or whatever else you come up with.  This is optional, but recommended.</li>
    <li>Next, create a few questions for the quiz using the "Questions" menu item.</li>
    <li>Once you're ready, create a quiz using the "Quizzes" menu item. Configure it according to your preferences (read the field descriptions)</li>
    <li>Finally, navigate to the "Details/Questions" page under each Quiz and add the respective questions to the quiz.</p>
    <li>You can add a link to the quiz using Joomla's menu manager, or link to it directly by copying the link from the preview.</p>
</ol>
<h3>Permission Config</h3>
<p>This component respects edit and delete permissions set in the global component options.</p>
<p>For example, if you don't want a user group to be able to delete a quiz or question, set the "Delete" permission to denied.</p>
<p>The edit permission is extended to create as well. Users require edit permissions to edit/create any question or quiz.</p>
<p>All other permissions are basically ignored, for now. You should probably just leave them as-is.</p>
<h3>MathJax</h3>
<p>To properly display math equations in the quiz, enable the MathJax setting under the global component options.</p>
<p>This allows you to format equations properly with LaTeX.</p>
<p>Once enabled, you must wrap your equations using backslash then parenthesis like so:</p>
<pre>\(y=mx+b\)</pre>
<p>You can try a tool like this <a href="https://latexeditor.lagrida.com/" target="_blank">Latex Editor</a> to help you format your equations.</p>

<h3>Misc Tips</h3>
<ul>
    <li>Multi page quizzes may take longer to load overall than a single page quiz.</li>
    <li>This extension is best suited to shorter quizzes with 20-30 questions or fewer. Anything longer than that may become difficult to manage and you might lose your visitors attention.</li>
    <li>If you use images, avoid having the answer in the image's filename. For example, if the question is to figure out what country's flag is being shown, don't name the file "JapanFlag.png".</li>
    <li>Avoid overly complex short-answer/fill-in-blank questions. Try limiting answers to simple 1-2 word responses (or a number).</li>
    <li>Avoid trick questions. Don't include multiple very similar options that could be easily confused. Properly check your own grammar and make sure there aren't actually multiple possible correct answers. You don't want users failing your quizzes when they actually knew the material just because your semantics are confusing.</li>
  </ul>
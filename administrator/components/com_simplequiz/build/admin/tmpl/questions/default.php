<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\Questions;
use KevinsGuides\Component\SimpleQuiz\Administrator\Helper\SimpleQuizHelper;
//this the template to display 1 quiz info

defined('_JEXEC') or die;



$sqhelper = new SimpleQuizHelper();

//get model
$this->model = $this->getModel();
$this->categories = $this->model->getCategories();

//check for category id _POST
if(isset($_POST['category_id']) && $_POST['category_id'] > 0){
    $this->category_id = $_POST['category_id'];
    //get items from this category
    $items = $this->model->getItemsByCategory($this->category_id);
    
}
else{
    $this->category_id = 0;
    //get items
    $items = $this->items;
}




?>

<h1>Questions Manager</h1>
<a class="btn btn-lg btn-success" href="index.php?option=com_simplequiz&view=Question&layout=edit">Add New Question</a>
<br/><br/>
<form id="question-category-selector" action="index.php?option=com_simplequiz&view=Questions" method="post">
    <select name="category_id" onchange="this.form.submit()">
        <option value="0">All Categories</option>
        <?php foreach($this->categories as $category): ?>
            <option value="<?php echo $category->id; ?>" <?php if($category->id == $this->category_id) echo 'selected'; ?>><?php echo $category->title; ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if($items != null): ?>
    <?php foreach($items as $item): ?>
        <div class="card mb-2 card-body">
            <p><?php echo $item->question; ?></p>
            <p>Category: <?php echo $sqhelper->getCategoryName($item->catid); ?></p>
            <a href="index.php?option=com_simplequiz&view=Question&layout=edit&qnid=<?php echo $item->id; ?>">Edit</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No questions found</p>
<?php endif; ?>
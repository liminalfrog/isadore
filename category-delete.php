<?php

// main file

$page_name = "Delete Category";

// import assets
require('assets/header.php');
require('assets/database.php');
require('assets/functions.php');
require('assets/searchbar.php');
require('assets/nav.php');

$sql = '
SELECT cat_name FROM categories WHERE cat_id = '.$id.';
';

$result = $connection->query($sql);

if($result->num_rows > 0){
	// output
	while($row = $result->fetch_assoc()){
		$error_no_category = false;
		// get item data
		$cat_name	= $row['cat_name'];

		// change page name to category
		$page_name = change_title('Deleting Category: '.$cat_name);
	}
} else {
	$error_no_category = true;
}

$connection->close();

require('assets/toolbar-begin.php');

// Product found; show content
if (!$error_no_category){

require('assets/toolbar-end.php');

?>

<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div class="btn-group me-2">
				<a type="button" class="btn btn-sm btn-outline-secondary" href="category-edit.php?id=<?php echo $id; ?>">&larr; Go Back</a>
			</div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <hr />
      <p>Are you sure you want to delete this category?</p>
      <p><strong>This action cannot be undone!</strong></p>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <a type="button" href="assets/category-delete.php?id=<?php echo $id; ?>" class="btn btn-sm btn-danger">Delete Category</a> <a type="button" href="category-edit.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-secondary">Cancel &amp; Go Back</a>
    </div>
  </div>

</div>
<script>
// Go back
document.addEventListener("keyup", function(event){
  if (event.keyCode === 27){
    window.location.href = "category-edit.php?id=<?php echo $id; ?>";
  }
});
</script>

<?php
} else { // Category not found; deliver error
	echo '<div class="container">
		<div class="row">
		<div class="col">';
	alert_danger("No category selected.");
	echo '
	</div>
	</div>
	</div>';
}


// get footer
require('assets/footer.php');

?>

<?php

// main file

$page_name = "Delete Item";

// import assets
require('assets/header.php');
require('assets/database.php');
require('assets/functions.php');
require('assets/searchbar.php');
require('assets/nav.php');

$sql = '
SELECT item_name FROM items WHERE item_id = '.$id.';
';

$result = $connection->query($sql);

if($result->num_rows > 0){
	// output
	while($row = $result->fetch_assoc()){
		$error_no_item = false;
		// get item data
		$item_name			= $row['item_name'];

		// change page name to item
		$page_name = change_title('Deleting Item: '.$item_name);
	}
} else {
	$error_no_item = true;
}

$connection->close();

require('assets/toolbar-begin.php');

// Product found; show content
if (!$error_no_item){

require('assets/toolbar-end.php');

?>

<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div class="btn-group me-2">
				<a type="button" class="btn btn-sm btn-outline-secondary" href="item-edit.php?id=<?php echo $id; ?>">&larr; Go Back</a>
			</div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <hr />
      <p>Are you sure you want to delete this item?</p>
      <p><strong>This action cannot be undone!</strong></p>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <a type="button" href="assets/item-delete.php?id=<?php echo $id; ?>" class="btn btn-sm btn-danger">Delete Item</a> <a type="button" href="item-view.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-secondary">Cancel &amp; Go Back</a>
    </div>
  </div>

</div>


<?php
} else { // Item not found; deliver error
	echo '<div class="container">
		<div class="row">
		<div class="col">';
	alert_danger("No item selected.");
	echo '
	</div>
	</div>
	</div>';
}


// get footer
require('assets/footer.php');

?>

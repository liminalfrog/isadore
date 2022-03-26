<?php

// main file

$page_name = "View Item";

// import assets
require('assets/header.php');
require('assets/database.php');
require('assets/functions.php');
require('assets/searchbar.php');
require('assets/nav.php');

$sql = '
SELECT
	i.item_name AS Name,
	i.item_code as Code,
	i.item_quantity as Quantity,
	i.item_qty2order as "Qty-to-Order",
	v.ven_name AS Vendor,
	i.item_link as Link,
	c.cat_name as Category,
	i.item_notes as Notes,
	DATE_FORMAT(i.item_dateadded, \'%c/%d/%Y | %r\') as Date
FROM items as i
	LEFT OUTER JOIN vendors as v ON i.ven_id = v.ven_id
	LEFT OUTER JOIN categories AS c ON i.cat_id = c.cat_id
WHERE i.item_id = '.$id.'
ORDER BY Category ASC;
';

$result = $connection->query($sql);

if($result->num_rows > 0){
	// output
	while($row = $result->fetch_assoc()){
		$error_no_item = false;
		// get item data
		$item_name			= $row['Name'];
		$item_code			= $row['Code'];
		$item_quantity	= $row['Quantity'];
		$item_qty2order	= $row['Qty-to-Order'];
		$item_vendor		= $row['Vendor'];
		$item_link			= $row['Link'];
		$item_category	= $row['Category'];
		$item_notes			= $row['Notes'];
		$item_dateadded	= $row['Date'];

		// change page name to item
		$page_name = change_title($item_name);
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
				<a class="btn btn-sm btn-outline-secondary" href="index.php">&larr; Go Back</a>
				<a class="btn btn-sm btn-outline-secondary" href="item-edit.php?id=<?php echo $id; ?>">Edit Item</a>
			</div>

<?php

// ITEM MESSAGES

// low in stock
if($item_quantity <= $item_qty2order){
	alert_warning("Low in stock. Time to reorder!");
}

// out of stock
if ($item_quantity == 0) {
	alert_danger("This item is out of stock.");
}

// changes saved successfully
if (isset($saved) && $saved == true) {
	alert_success("Changes saved successfully!");
}

// changes not saved
if (isset($failed) && $failed == true) {
	alert_danger("Error! Failed to save changes. Could not write to database.");
}

// error: delete item -> could not delete (unknown)
if($error_delete_item_failed)
  alert_danger("Could not delete item. (Error unknown)");

// Item information
echo "
<table class=\"table\">
	<thead class=\"thead thead-dark\">
		<tr>
			<td colspan=\"2\">Item Information</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Name</td>
			<td>$item_name</td>
		</tr>
		<tr>
			<td>Product Code/No.</td>
			<td>$item_code</td>
		</tr>
		<tr>
			<td>Category</td>
			<td>$item_category</td>
		</tr>
		<tr>
			<td>Quantity in Stock</td>
			<td>$item_quantity</td>
		</tr>
		<tr>
			<td>Quantity-to-Order</td>
			<td>$item_qty2order</td>
		</tr>
		<tr>
			<td>Vendor</td>
			<td>$item_vendor</td>
		</tr>
		<tr>
			<td>Link to Order</td>
			<td><a href=\"$item_link\" target=\"_blank\">$item_link</a></td>
		</tr>
		<tr>
			<td>Date Added</td>
			<td>$item_dateadded</td>
		</tr>
	</tbody>
</table>
";

?>
</div><!-- table -->
<div class="col-sm-6">
		<div class="card" style="width: 18rem;">
		  <!--<img class="card-img-top" src="..." alt="Card image cap">-->
		  <div class="card-body">
		    <h5 class="card-title">Notes</h5>
		    <p class="card-text">
					<?php
						echo $item_notes;
						?>
				</p>
		  </div>
		</div>

</div><!-- img and notes -->


</div><!-- row -->
<div class="row">
	<div class="col">
		<a href="item-delete.php?id=<?php echo $id; ?>" type="button" class="btn btn-sm btn-outline-danger">Delete Item</a>
	</div>
</div>
</div><!-- container -->

<script>
	// go back
	document.addEventListener("keyup", function(event){
		if (event.keyCode === 27){
			window.location.href = "/";
		} else if (event.keyCode === 69){
			window.location.href = "item-edit.php?id=<?php echo $id; ?>";
		}
	});
</script>

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

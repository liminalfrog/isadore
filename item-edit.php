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
  i.ven_id as VendorID,
	v.ven_name AS Vendor,
	i.item_link as Link,
  i.cat_id as CategoryID,
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
    $item_ven_id    = $row['VendorID'];
		$item_vendor		= $row['Vendor'];
		$item_link			= $row['Link'];
    $item_cat_id    = $row['CategoryID'];
		$item_category	= $row['Category'];
		$item_notes			= $row['Notes'];
		$item_dateadded	= $row['Date'];

		// change page name to item
		$page_name = change_title('Editing: '.$item_name);
	}
} else {
	$error_no_item = true;
}

require('assets/toolbar-begin.php');

// Product found; show content
if (!$error_no_item){

require('assets/toolbar-end.php');

?>

<form action="assets/item-save.php" class="needs-validation" method="post" novalidate>
<div class="container">
	<div class="row">
		<div class="col">
			<div class="btn-group me-2">
				<a class="btn btn-sm btn-outline-secondary" href="item-view.php?id=<?php echo $id; ?>">&larr; Cancel</a>
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
if (isset($saved) && $save == 'success') {
	alert_success("Changes saved successfully!");
}

// Item information
echo '
<table class="table">
	<thead class="thead thead-dark">
		<tr>
			<td colspan="2">Item Information</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Name (required)</td>
			<td><input class="form-control form-control-sm" type="text" name="item_name" value="'.htmlentities($item_name).'" required></td>
		</tr>
		<tr>
			<td>Product Code/No.</td>
			<td><input class="form-control form-control-sm" type="text" name="item_code" value="'.htmlentities($item_code).'" ></td>
		</tr>
    <tr>
			<td>Categories</td>
			<td>
			<label for="cat_new" class="small">Type here to create new category:</label>
			<input id="cat_new" class="form-control form-control-sm" type="text" name="item_new_cat" placeholder="Enter category name" onfocus="switchLabel()">
			<label id="cat_label" for="cat_dropdown" class="small">Or, select from existing categories:</label>
			<select id="cat_dropdown" name="cat_id" class="form-control form-control-sm">';
      $db = new PDO("mysql:host=$server;dbname=$database", $username, $password);

      //turn on exceptions
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //set default fetch mode
      $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

      function generateTree($data, $parent = 0, $depth=0)
      {
          for ($i=0, $ni=count($data); $i < $ni; $i++) {
              if ($data[$i]['cat_parent'] == $parent) {
                  global $item_cat_id;
                  $tree .= '<option value="'.$data[$i]['cat_id'].'"';
                  // current category
                  if ($data[$i]['cat_id'] == $item_cat_id)
                    $tree .= ' selected';
                  $tree .= '>';
                  if ($depth > 0){
                    for ($z = 1; $z <= $depth; $z++)
                      $tree .= '&emsp;';
                  }
                  $tree .= $data[$i]['cat_name'];
                  $tree .= '</option>';
                  $tree .= generateTree($data, $data[$i]['cat_id'], $depth+1);
              }
          }
          return $tree;
      }

      $categories = $db->query('SELECT cat_id, cat_parent, cat_name FROM categories ORDER BY cat_name');
      $rows = $categories->fetchAll(PDO::FETCH_ASSOC);
      echo generateTree($rows);

  echo '</select></td>
		</tr>
		<tr>
			<td>Quantity in Stock</td>
			<td><input class="form-control form-control-sm" type="text" name="item_quantity" value="'.$item_quantity.'" /></td>
		</tr>
		<tr>
			<td>Quantity-to-Order</td>
			<td><input class="form-control form-control-sm" type="text" name="item_qty2order" value="'.$item_qty2order.'" /></td>
		</tr>
		<tr>
			<td>Vendor</td>
			<td><select name="item_ven_id" class="form-control form-control-sm">
				<option value="">No Vendor</option>';

// get vendors
$vendor_sql = 'SELECT ven_id, ven_name FROM vendors ORDER BY ven_name ASC;';
$vendor_result = $connection->query($vendor_sql);
if($vendor_result->num_rows > 0){
	// output
	while($row = $vendor_result->fetch_assoc()){
    echo '<option value="'.$row['ven_id'].'"';
    if ($row['ven_id'] == $item_ven_id)
      echo ' selected';
    echo '>'.$row['ven_name'].'</option>';
  }
}

echo '</select></td>
		</tr>
		<tr>
			<td>Link to Order</td>
			<td><input class="form-control form-control-sm" type="text" name="item_link" value="'.htmlentities($item_link).'" /></a></td>
		</tr>
		<tr>
			<td>Date Added</td>
			<td><input class="form-control form-control-sm" type="text" name="item_dateadded" value="'.$item_dateadded.'" disabled /></td>
		</tr>
	</tbody>
</table>
';

?>
<script>
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();


	function switchLabel() {
		document.getElementById("cat_label").innerHTML = '<b>Select a parent category. (Optional)</b>';
	}

	// cancel
	document.addEventListener("keyup", function(event){
		if (event.keyCode === 27){
			window.location.href = "item-view.php?id=<?php echo $id; ?>";
		}
	});
</script>
</div><!-- table -->
<div class="col-sm-6">
		<div class="card" style="width: 18rem;">
		  <!--<img class="card-img-top" src="..." alt="Card image cap">-->
		  <div class="card-body">
		    <h5 class="card-title">Notes</h5>
		    <p class="card-text">
          <textarea name="item_notes"><?php
						echo htmlentities($item_notes);
						?></textarea>
				</p>
		  </div>
		</div>

</div><!-- img and notes -->


</div><!-- row -->

<!-- submit form -->
<div class="row">
  <div class="col">
    <!-- add id to send -->
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <input type="submit" class="btn btn-sm btn-primary" value="Save Changes" />
    <input type="reset" class="btn btn-sm btn-outline-secondary" value="Start Over" />
  </div>
</div>

</div><!-- container -->

</form>

<?php
} else { // Item not found; deliver error
	echo '<div class="container">
		<div class="row">';
	alert_danger("No item selected.");
	echo '</div>
	</div>';
}


// get footer
require('assets/footer.php');

?>

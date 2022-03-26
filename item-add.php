<?php

// main file

$page_name = "Add New Item";

// import assets
require('assets/header.php');
require('assets/database.php');
require('assets/functions.php');
require('assets/searchbar.php');
require('assets/nav.php');
require('assets/toolbar-begin.php');

change_title($page_name);

require('assets/toolbar-end.php');

?>

<form action="assets/item-create.php" class="needs-validation" method="post" novalidate>
<div class="container">
	<div class="row">
		<div class="col">
			<div class="btn-group me-2">
				<a class="btn btn-sm btn-outline-secondary" href="index.php">&larr; Cancel</a>
			</div>

<?php

// ITEM MESSAGES

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
			<td><input class="form-control form-control-sm" type="text" name="item_name" autocomplete="off" autofocus required></td>
		</tr>
		<tr>
			<td>Product Code/No.</td>
			<td><input class="form-control form-control-sm" type="text" name="item_code" ></td>
		</tr>
    <tr>
			<td>Categories</td>
			<td>
			<label for="cat_new" class="small">Type here to create new category:</label>
			<input id="cat_new" class="form-control form-control-sm" type="text" name="item_new_cat" placeholder="Enter category name" onfocus="switchLabel()">
			<label id="cat_label" for="cat_dropdown" class="small">Or, select from existing categories:</label>
			<select id="cat_dropdown" name="item_cat_id" class="form-control form-control-sm">';
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
                  if ($data[$i]['cat_id'] == 1)
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
			<td><input class="form-control form-control-sm" type="text" name="item_quantity"></td>
		</tr>
		<tr>
			<td>Quantity-to-Order</td>
			<td><input class="form-control form-control-sm" type="text" name="item_qty2order"></td>
		</tr>
		<tr>
			<td>Vendor</td>
			<td><select name="item_ven_id" class="form-control form-control-sm">
        <option value="" selected>Select Vendor</option>
        ';

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
			<td><input class="form-control form-control-sm" type="text" name="item_link" /></a></td>
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
// Go back
document.addEventListener("keyup", function(event){
  if (event.keyCode === 27){
    window.location.href = "index.php";
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
          <textarea name="item_notes"></textarea>
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

// get footer
require('assets/footer.php');

?>

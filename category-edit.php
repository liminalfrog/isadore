<?php

// main file

$page_name = "Edit Category";

// import assets
require('assets/header.php');
require('assets/database.php');
require('assets/functions.php');
require('assets/searchbar.php');
require('assets/nav.php');

$sql = 'SELECT cat_name, cat_parent FROM categories WHERE cat_id = '.$id.';';

$result = $connection->query($sql);

if($result->num_rows > 0){
	// output
	while($row = $result->fetch_assoc()){
		$error_no_item = false;
		// get data
    $cat_name   = $row['cat_name'];
    $cat_parent = $row['cat_parent'];

		// change page name to item
		$page_name = change_title('Editing: '.$cat_name);
	}
} else {
	$error_no_category = true;
}

require('assets/toolbar-begin.php');

// Product found; show content
if (!$error_no_category){

require('assets/toolbar-end.php');

?>

<form action="assets/category-save.php" class="needs-validation" method="post" novalidate>
<div class="container">
	<div class="row">
		<div class="col">
			<a class="btn btn-sm btn-outline-secondary" href="category.php">&larr; Cancel</a>

<?php

// CATEGORY MESSAGES

// changes saved successfully
if ($saved)
	alert_success("Changes saved successfully!");

if ($failed)
	alert_danger("Error: Could not save changes.");

// Category information
echo '
<table class="table">
	<thead class="thead thead-dark">
		<tr>
			<td colspan="2">Category Information</td>
		</tr>
	</thead>
	<tbody>
    <tr>
			<td>Name (Required)</td>
			<td>
			<input class="form-control form-control-sm" type="text" name="cat_name" value="'.$cat_name.'" autocomplete="off" placeholder="Enter category name" required>
      </td>
    </tr>
    <tr>
      <td>Parent Category (Optional)</td>
      <td>
      <select name="cat_parent" class="form-control form-control-sm">
        <option value="0" selected>No parent</option>';
      $db = new PDO("mysql:host=$server;dbname=$database", $username, $password);

      //turn on exceptions
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //set default fetch mode
      $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

      function generateTree($data, $parent = 0, $depth=0)
      {
          global $cat_parent;
          for ($i=0, $ni=count($data); $i < $ni; $i++) {
              if ($data[$i]['cat_parent'] == $parent) {
                  global $item_cat_id;
                  $tree .= '<option value="'.$data[$i]['cat_id'].'"';
                  // current category
                  if ($data[$i]['cat_id'] == $cat_parent)
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
			window.location.href = "category.php";
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
						echo $item_notes;
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
    <input type="reset" class="btn btn-sm btn-outline-secondary" value="Start Over" />&emsp;
    <a type="button" href="category-delete.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-danger">Delete Category</a>
  </div>
</div>

</div><!-- container -->

</form>

<?php
} else { // Category not found; deliver error
	echo '<div class="container">
		<div class="row">';
	alert_danger("No category selected.");
	echo '</div>
	</div>';
}


// get footer
require('assets/footer.php');

?>

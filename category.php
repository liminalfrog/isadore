<?php

// main file
$page_name = "Categories";

// import assets
require('assets/includes.php');
page_list();
require('assets/toolbar-begin.php');

?>

          <div class="btn-group me-2">
            <a href="category-add.php" type="button" class="btn btn-sm btn-outline-secondary">+ Add New Category</a>
          </div>


<?php

require('assets/toolbar-end.php');

// MESSAGES

// error: invalid ID
if($error_invalid_id)
  alert_danger("Error: Invalid ID");

// error: save changes -> no id selected
if($error_save_changes_no_id)
  alert_danger("Could not save changes: No ID selected.");

// error: delete category -> no id selected
if($error_delete_cat_no_id)
  alert_danger("Could not delete category: No ID selected.");

// error: delete category -> could not delete
if($error_delete_category_failed)
  alert_danger("Could not delete category: Failed to change database.");

// error: create new category -> could not write to database
if($error_add_new_cat_write)
  alert_success('Category not added:  could not write to database. (Error unknown)');

// success: created new category
if($success_add_new_cat)
  alert_success('Category successfully created!&emsp;<a class="alert-link" href="category-edit.php?id='.$id.'">View new category &rarr;</a>');

// success: deleted category
if($success_delete_cat)
  alert_warning('Category successfully deleted.'); // use warning colors

?>

<!-- begin table -->
<div class="table-responsive">
        <table class="table table-striped table-sm table-hover">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Name</th>
            </tr>
          </thead>
          <tbody id="tableRows">


<?php

// Get all categories and list with child subcategories

$db = new PDO("mysql:host=$server;dbname=$database", $username, $password);

//turn on exceptions
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//set default fetch mode
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

function generateTree($data, $parent = 0, $depth=0)
{
    for ($i=0, $ni=count($data); $i < $ni; $i++) {
        if ($data[$i]['cat_parent'] == $parent) {
            $tree .= '<tr>
            <td>'.$data[$i]['cat_id'].'</td>
            <td><a href="category-edit.php?id='.$data[$i]['cat_id'].'" class="text-dark">';
            if ($depth > 0){
              for ($z = 1; $z <= $depth; $z++)
                $tree .= '&emsp;';
              $tree .= '&rdsh;';
            }
            $tree .= $data[$i]['cat_name'];
            $tree .= '</a></td>
            </tr>';
            $tree .= generateTree($data, $data[$i]['cat_id'], $depth+1);
        }
    }
    return $tree;
}

$categories = $db->query('SELECT cat_id, cat_parent, cat_name FROM categories ORDER BY cat_name');
$rows = $categories->fetchAll(PDO::FETCH_ASSOC);
echo generateTree($rows);

?>
          </tbody><!-- tableRows -->
        </table>
      </div>

      <script>
        function searchQuery(str) {
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("tableRows").innerHTML = this.responseText;
            }
          };
          xhttp.open("GET", "assets/category-search.php?str="+str, true);
          xhttp.send();
        }

        function minusQuantity(num){
          document.getElementById("msg" + num).innerHTML = "";
          itemInput = document.getElementById("input"+num);
          amount = parseInt(itemInput.value) - 1;
          document.getElementById("input"+num).value = amount;
        }

        function plusQuantity(num){
          document.getElementById("msg" + num).innerHTML = "";
          itemInput = document.getElementById("input"+num);
          amount = parseInt(itemInput.value) + 1;
          document.getElementById("input"+num).value = amount;
        }

        function saveQuantity(inputID) {
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("msg" + inputID).innerHTML = this.responseText;
            }
          };
          // get quantity
          qty = document.getElementById("input"+inputID).value;
          if (qty < 0){
            qty = 0;
            document.getElementById("input"+inputID).value = "0";
          }
          xhttp.open("GET", "assets/item-quick-quantity.php?id="+inputID+"&item_quantity="+qty, true);
          xhttp.send();
        }
        // Add new category
        document.addEventListener("keyup", function(event){
          if (event.keyCode === 107){
            window.location.href = "category-add.php";
          }
        });
      </script>


<?php

$connection->close();

get_footer();

?>

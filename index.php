<?php

// main file
$page_name = "Inventory Database";
$page_list = true;

// import assets
require('assets/header.php');
require('assets/database.php');
require('assets/functions.php');
require('assets/searchbar.php');
require('assets/nav.php');
require('assets/toolbar-begin.php');

?>

<div class="btn-group me-2">
  <a href="item-add.php" type="button" class="btn btn-sm btn-outline-secondary">+ Add New Item</a>
</div>


<?php

require('assets/toolbar-end.php');

// MESSAGES

// error: invalid ID
if($error_invalid_id)
  alert_danger("Error: Invalid ID");

// error: save changes -> no id selected
if($error_save_changes_no_id)
  alert_danger("Could not save changes:  no ID selected.");

// error: delete item -> no id selected
if($error_delete_item_no_id)
  alert_danger("Could not delete item:  no ID selected.");

// error: create new item -> could not write to database
if($error_add_new_item_write)
  alert_success('Item not added:  could not write to database. (Error unknown)');

// success: created new item
if($success_add_new_item)
  alert_success('Item successfully created!&emsp;<a class="alert-link" href="item-view.php?id='.$id.'">View new item &rarr;</a>');

// success: deleted item
if($success_delete_item)
  alert_warning('Item successfully deleted.'); // use warning colors

?>

<!-- begin table -->
<div class="row">
  <div class="col">
    <a href="/" type="button" class="btn btn-sm btn-outline-primary border-0">All Items</a> |&nbsp;
    <a href="/?top=10" type="button" class="btn btn-sm btn-outline-primary border-0<?php
    if($top == 10)
      echo ' active';
    ?>">Top 10 Most Edited</a> |&nbsp;
  </div>
  <div class="col">
    <select class="form-control form-control-sm custom-select custom-select-sm">
      <option selected disabled>Filter by Category</option>
    </select>
  </div>
  <div class="col">
    <select class="form-control form-control-sm custom-select custom-select-sm">
      <option selected disabled>Filter by Status</option>
    </select>
  </div>
  <div class="col">
    <select class="form-control form-control-sm custom-select custom-select-sm">
      <option selected disabled>Filter by Vendor</option>
    </select>
  </div>
</div>
<div class="table-responsive">
        <table class="table table-striped table-sm table-hover">
          <thead>
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Code</th>
              <th scope="col">Category</th>
              <th scope="col">Quantity</th>
              <th scope="col">Vendor</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody id="tableRows">


<?php

require('assets/get_statuses.php');

if($top){
  if ($top == 10){
    echo $sql = 'SELECT
      i.item_id AS ID,
      i.item_name AS Name,
      i.item_code AS Code,
      c.cat_name AS Category,
      i.item_quantity AS Quantity,
      v.ven_name AS Vendor,
      i.stat_id AS StatusID,
      s.stat_name AS Status,
      s.stat_style AS Style,
      e.edit_date
    FROM item_edits AS e
    LEFT OUTER JOIN items AS i ON i.item_id = e.item_id
    LEFT OUTER JOIN vendors AS v ON i.ven_id = v.ven_id
    LEFT OUTER JOIN categories AS c ON i.cat_id = c.cat_id
    LEFT OUTER JOIN statuses AS s ON i.stat_id = s.stat_id
    GROUP BY i.item_name
    ORDER BY e.edit_date DESC LIMIT 10;';
  }
} else { // Default SQL
  $sql = '
  SELECT
    i.item_id AS ID,
    i.item_name AS Name,
    i.item_code AS Code,
    c.cat_name AS Category,
    i.item_quantity AS Quantity,
    v.ven_name AS Vendor,
    i.stat_id AS StatusID,
    s.stat_name AS Status,
    s.stat_style AS Style
  FROM items AS i
  LEFT OUTER JOIN categories AS c ON i.cat_id = c.cat_id
  LEFT OUTER JOIN vendors AS v ON i.ven_id = v.ven_id
  LEFT OUTER JOIN statuses AS s ON i.stat_id = s.stat_id
  ORDER BY Category ASC;
  ';
}



$result = $connection->query($sql);

if($result->num_rows > 0){
	// output
	while($row = $result->fetch_assoc()){
    echo '<tr>
		<td><a class="text-dark" href="item-view.php?id='.$row['ID'].'">' . $row['Name'] . '</td>
		<td><a class="text-dark" href="item-view.php?id='.$row['ID'].'">' . $row['Code'] . '</td>
		<td>' . $row['Category'] . '</td>
		<td>
			<div class="btn-group btn-group-sm mr-2" role="group" aria-label="First group">
				<input id="input'.$row['ID'].'" onfocus="listen('.$row['ID'].')" class="form-control form-control-sm" type="text" name="item_quantity" value="' . $row['Quantity'] . '" size="6" />
 				<button type="button" class="btn btn-danger" onclick="minusQuantity('.$row['ID'].')">&#9660;</button>
				<button type="button" class="btn btn-primary" onclick="plusQuantity('.$row['ID'].')">&#9650;</button>
			</div>
      &emsp;<button type="button" class="btn btn-sm btn-outline-success" onclick="saveQuantity('.$row['ID'].')" id="quantitySaveButton'.$row['ID'].'">Save</button><span class="badge bg-primary" id="msg'.$row['ID'].'"></span>
		</td>
		<td>' . $row['Vendor'] . '</td>
    <td><select id="select'.$row['ID'].'" class="badge  bg-'.$row['Style'].'" onchange="changeStatus(this.value, '.$row['ID'].')">';
      foreach ($statuses as $key => $value){
        echo '<option value="'.$key.'" class="bg-'.$styles[$key].'"';
        // current status
        if ($key == $row['StatusID'])
          echo ' selected';
        echo '>'.$value.'</option>';
      }
      echo '
    </select><span class="badge bg-primary" id="statusMsg'.$row['ID'].'"></span></td>
		</tr>';
	}
} else {
	echo '<tr>
	<td colspan="6">No results</td>

	</tr>';
}

?>
          </tbody><!-- tableRows -->
        </table>
      </div>

      <script>
        // escape
        document.addEventListener("keyup", function(event){
          if (event.keyCode === 27) {
            document.getElementById("searchBar").value = '';
            searchQuery('');
          }
        });

        // Request rows based on user input
        function searchQuery(str) {
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("tableRows").innerHTML = this.responseText;
            }
          };
          xhttp.open("GET", "assets/item-search.php?filter=<?php echo $filter; ?>&str="+str, true);
          xhttp.send();
          // Go to item page when "enter" pressed
          document.getElementById("searchBar").addEventListener("keyup", function(event){
            if (event.keyCode === 13) {
              if (str != ''){
                window.location.href = document.getElementById("itemLinkID").href;
              }
            } else if (event.keyCode === 107) {
              window.location.href = "item-add.php";
            }
          });
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

        function listen(inputID) {
          qtyInput = document.getElementById("input" + inputID);
          qtyInput.addEventListener("keyup", function(event){
            if (event.keyCode === 13){
              document.getElementById("quantitySaveButton" + inputID).click();
            }
          });
        }

        // Quickly change status
        function changeStatus(selectedIndex, itemID){
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("statusMsg" + itemID).innerHTML = this.responseText;
              // Get style names
              const styles = [];
              <?php
                foreach ($styles as $key => $value){
                  echo 'styles['.$key.'] = "'.$value.'";
                  ';
                }
               ?>
              // Change select background color
              selectMenu = document.getElementById("select" + itemID);
              selectMenu.className = "badge bg-" + styles[selectedIndex];
            }
          };
          xhttp.open("GET", "assets/item-quick-status.php?str="+selectedIndex+"&id="+itemID, true);
          xhttp.send();
        }

        function saveQuantity(inputID) {
          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("msg" + inputID).innerHTML = this.responseText;
              // Get style names
              const styles = [];
              <?php
                foreach ($styles as $key => $value){
                  echo 'styles['.$key.'] = "'.$value.'";
                  ';
                }
               ?>
              // Change selected status index
              hiddenSelectedIndex = document.getElementById("hiddenSelectedIndex" + inputID).value;
              selectMenu = document.getElementById("select"+inputID);
              selectMenu.className = "badge bg-" + styles[hiddenSelectedIndex];
              selectMenu.selectedIndex = hiddenSelectedIndex - 1; // (-1 to fix offset)
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
      </script>


<?php

$connection->close();

// get footer
$go_home = true; // see footer.php
require('assets/footer.php');

?>

<?php

// Save item quantities from the index

// Get database info
require('database.php');

//if(!isset($id) || $id == ''){
  //echo 'Error!';
//} else {
  // Fix quantity
  if ($item_quantity == '')
    $item_quantity = 'null';
  if ($item_quantity < 0)
    $item_quantity = "0";

  // Update table
  $sql = 'UPDATE items SET item_quantity = '.$item_quantity.' WHERE item_id = '.$id.';';

  if($connection->query($sql) === true){

    // Get quantity to order and check if above
    $quantity_sql = 'SELECT item_qty2order FROM items WHERE item_id = '.$id.';';
    $result = $connection->query($quantity_sql);
    while ($data = $result->fetch_assoc())
      $qty2order = $data['item_qty2order'];
    if ($item_quantity > $qty2order){
      // set status to "In Stock"
      $selected_index = 1;
    } else if ($item_quantity <= $qty2order){
      // Is it zero?
      if ($item_quantity == 0){ // Mark as out of stock
        $selected_index = 2;
      } else { // Mark for reordering
        $selected_index = 4;
      }
    } else if ($item_quantity == 0){ // Mark as out of stock
      $selected_index = 2;
    }
    $status = 'UPDATE items SET stat_id = '.$selected_index.' WHERE item_id = '.$id.';';
    $connection->query($status);

    // add edit record to database
    require('item-edit-record.php');

    echo '&#10003;<input type="hidden" name="selectedIndex'.$id.'" id="hiddenSelectedIndex'.$id.'" value="'.$selected_index.'" />';
  } else {
    echo 'Error!';
  }
//}

 ?>

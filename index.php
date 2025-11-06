<?php
session_start();

include('./includes/header.php');
include('./includes/config.php');
?>

<div class="container mt-3">
  <?php include('./includes/alert.php'); ?>

  <?php
  // Login status / greeting
  if (isset($_SESSION['user_id'])) {
      // prefer a stored name if available, otherwise fallback to email
      $displayName = $_SESSION['name'] ?? $_SESSION['email'] ?? 'User';
      $role = $_SESSION['role'] ?? 'customer'; // default to customer if not set

      // sanitize for output
      $displayNameSafe = htmlspecialchars($displayName);
      $roleSafe = htmlspecialchars(ucfirst($role)); // show "Customer" or "Admin"

      echo "<div class='alert alert-secondary'>Hello, <strong>{$displayNameSafe}</strong> <span class='text-muted'>({$roleSafe})</span></div>";
  } else {
      echo '<div class="alert alert-info">You are browsing as a <strong>guest</strong>. <a href="/shop/user/login.php">Login</a> or <a href="/shop/user/register.php">Register</a></div>';
  }
  ?>
</div>

<?php
// Cart display (unchanged logic, just removed debug print)
if (isset($_SESSION["cart_products"]) && count($_SESSION["cart_products"]) > 0) {
    echo '<div class="cart-view-table-front container" id="view-cart">';
    echo '<h3>Your Shopping Cart</h3>';
    echo '<form method="POST" action="cart_update.php">';
    echo '<table class="table" width="100%"  cellpadding="6" cellspacing="0">';
    echo '<tbody>';
    $total = 0;
    $b = 0;
    foreach ($_SESSION["cart_products"] as $cart_itm) {
        $product_name = htmlspecialchars($cart_itm["item_name"]);
        $product_qty = (int)$cart_itm["item_qty"];
        $product_price = (float)$cart_itm["item_price"];
        $product_code = htmlspecialchars($cart_itm["item_id"]);
        $bg_color = ($b++%2 == 1) ? 'odd' : 'even';
        echo '<tr class="'.$bg_color.'">';
        echo "<td>Qty <input type='number' size='2' maxlength='2' name='product_qty[$product_code]' value='{$product_qty}' /></td>";
        echo "<td>{$product_name}</td>";
        echo '<td><input type="checkbox" name="remove_code[]" value="'.$product_code.'" /> Remove</td>';
        echo '</tr>';
        $subtotal = ($product_price * $product_qty);
        $total += $subtotal;
    }
    echo '<tr><td colspan="4">';
    echo '<button type="submit" class="btn btn-secondary">Update</button> <a href="view_cart.php" class="btn btn-success">Checkout</a>';
    echo '</td></tr>';
    echo '</tbody>';
    echo '</table>';
    echo "</form>";
    echo '</div>';
}

// Product list
$sql = "SELECT i.item_id AS itemId, i.description, i.image_path, i.sell_price 
        FROM item i 
        INNER JOIN stock s USING (item_id)
        ORDER BY i.item_id ASC";

$results = mysqli_query($conn, $sql);

if ($results) {
    $products_item = '<ul class="products list-unstyled container d-flex flex-wrap gap-3">';

    while ($row = mysqli_fetch_assoc($results)) {
        $image = !empty($row['image_path']) ? $row['image_path'] : './item/images/default.png';
        $desc = htmlspecialchars($row['description']);
        $price = number_format((float)$row['sell_price'], 2);
        $itemId = (int)$row['itemId'];

        $products_item .= <<<EOT
        <li class="product card" style="width: 220px;">
            <form method="POST" action="cart_update.php">
                <div class="product-content p-2">
                    <h5 class="product-title">{$desc}</h5>
                    <div class="product-thumb text-center mb-2">
                        <img src="{$image}" alt="{$desc}" style="max-width:100%; height:100px; object-fit:cover;" />
                    </div>
                    <div class="product-info text-center mb-2">
                        <div class="fw-bold">₱{$price}</div>
                        <fieldset class="mb-2">
                            <label>
                                <span>Quantity</span>
                                <input type="number" class="form-control form-control-sm" style="width:70px; display:inline-block; margin-left:8px;" name="item_qty" value="1" />
                            </label>
                        </fieldset>
                        <input type="hidden" name="item_id" value="{$itemId}" />
                        <input type="hidden" name="type" value="add" />
                        <div class="d-grid"><button type="submit" class="btn btn-outline-primary btn-sm add_to_cart">Add</button></div>
                    </div>
                </div>
            </form>
        </li>
EOT;
    }

    $products_item .= '</ul>';
    echo $products_item;
}

include('./includes/footer.php');
?>

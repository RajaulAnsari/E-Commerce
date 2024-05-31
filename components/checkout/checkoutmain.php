<?php
// session_start();

// Check if the user is logged in
if (!isset($_SESSION['uusername'])) {
    header("Location: usersignin.php");
    exit();
}

if (isset($_POST['backToCart'])) {
    header("Location: cart.php");
    exit();
}
// Include database connection
include 'connection.php';

// Check if session variables for cart total and total items are set
$total = isset($_SESSION['cart_total']) ? $_SESSION['cart_total'] : 0;
$total_items = isset($_SESSION['cart_total_items']) ? $_SESSION['cart_total_items'] : 0;
$product_id = isset($_SESSION['product_id']) ? $_SESSION['product_id'] : 0;

// Collection dates logic...
$collectionDates = [];
$now = strtotime("now");
$end_date = strtotime("+2 weeks");

while (date("Y-m-d", $now) != date("Y-m-d", $end_date)) {
    $day_index = date("w", $now);  // Get the day of the week index (0 = Sunday, 6 = Saturday)
    if ($day_index == 3 || $day_index == 4 || $day_index == 5) {
        $timeDiff = abs(strtotime(date("Y-m-d", $now))) - abs(strtotime("+1 day")); 
        array_push($collectionDates, [date("F j, l", $now) => $timeDiff]);
    }
    $now = strtotime(date("Y-m-d", $now) . "+1 day");
}

// Check if form is submitted for confirmation of payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmPayment'])) {
    // Retrieve the selected collection date and time
    $collectionDate = isset($_POST['collectionDate']) ? $_POST['collectionDate'] : null;
    $collectionTime = isset($_POST['collectionTime']) ? $_POST['collectionTime'] : null;

    if ($collectionDate && $collectionTime) {
        // Store collection date and time in session
        $_SESSION['collectionDate'] = $collectionDate;
        $_SESSION['collectionTime'] = $collectionTime;

        // Redirect to store_collection_slot.php for further processing
        header("Location: store_collection_slot.php");
        exit();
    } else {
        // Handle case when date or time is not selected
        $selectedDateTimeError = "Please select both date and time for collection.";
    }
}
?>

<div class="container">
    </br>
    </br>
    <div class="collection-slot-title">
        <h2>Collection Slot</h2>
    </div>
    </br>
    </br>
    <div class="checkout-step">
        <form method="POST" id="checkoutForm" onsubmit="return handleFormSubmit(event);">
            <!-- Date selection step -->
            <div class="checkout-step-heading">Choose Collection Date</div>
            <div class="checkout-step-desc">This is the field where you will be choosing collection slots.</div>
            <!-- Date selection dropdown -->
            <div class="checkout-step-sub">
                <label for="collectionDate">Date of Collection:</label>
                <select name="collectionDate" id="collectionDate" required>
                    <option value="">---- Select Date here ----</option>
                    <!-- Loop through $collectionDates to populate options -->
                    <?php
                        foreach($collectionDates as $key=>$dates){
                            foreach($dates as $day=>$diff){
                                $isDisabled = $diff < 0 ? 'disabled' : '';
                                $isSelected = isset($selectedDate) && $selectedDate == $day ? 'selected' : '';
                                echo "<option $isSelected $isDisabled value='".$day."'>".$day."</option>";
                            }
                        }
                    ?>
                </select>
                <!-- Error message for date selection -->
                <?php if(isset($selectedDateError)): ?>
                <div class="input-error"><?php echo $selectedDateError; ?></div>
                <?php endif; ?>
            </div>

            <!-- Time selection step -->
            <div class="checkout-step-heading">Choose Collection Time</div>
            <div class="checkout-step-desc">Select the time of collection.</div>
            <!-- Time selection radio buttons -->
            <div class="checkout-step-sub">
                <label>
                    <input type="radio" name="collectionTime" value="10-13" required>10:00 AM - 1:00 PM
                </label>
                <label>
                    <input type="radio" name="collectionTime" value="13-16" required> 1:00 PM - 4:00 PM
                </label>
                <label>
                    <input type="radio" name="collectionTime" value="16-19" required> 4:00 PM - 7:00 PM
                </label>
                <!-- Error message for time selection -->
                <?php if(isset($selectedTimeError)): ?>
                <div class="input-error"><?php echo $selectedTimeError; ?></div>
                <?php endif; ?>
            </div>

            <!-- Payment step -->
            <div class="checkout-step-heading">Payment</div>
            <div class="checkout-step-desc">This is the field where you will be making payment.</div>
            <!-- Payment form -->
            <div class="checkout-step-sub">
                <label for="paymentMethod">Payment Method:</label>
                <select name="paymentMethod" id="paymentMethod">
                    <option value="paypal">PayPal</option>
                    <!-- Add more payment options if needed -->
                </select>
            </div>

            <!-- Include hidden input fields to pass total and total items -->
            <input type="hidden" name="total" value="<?php echo $total; ?>">
            <input type="hidden" name="total_items" value="<?php echo $total_items; ?>">
            <!-- Include hidden input fields to store selected collection date and time -->
            <input type="hidden" id="selectedCollectionDate" name="selectedCollectionDate" value="">
            <input type="hidden" id="selectedCollectionTime" name="selectedCollectionTime" value="">

            <div class="checkout-buttons">
                <!-- Payment form submission button -->
                <input type="submit" value="Cancel" class="btn primary-btn" name="backToCart" formnovalidate>
                <input type="submit" value="Confirm Payment" class="btn primary-btn" name="confirmPayment">
            </div>
        </form>
    </div>
</div>
</br>



<script>
function handleFormSubmit(event) {
    const collectionDate = document.getElementById('collectionDate').value;
    const collectionTime = document.querySelector('input[name="collectionTime"]:checked');
    const confirmPaymentBtn = document.querySelector('input[name="confirmPayment"]');

    if (event.submitter === confirmPaymentBtn) {
        if (!collectionDate) {
            alert('Please select a collection date.');
            return false;
        }

        if (!collectionTime) {
            alert('Please select a collection time.');
            return false;
        }

        // Set selected collection date and time in hidden input fields
        document.getElementById('selectedCollectionDate').value = collectionDate;
        document.getElementById('selectedCollectionTime').value = collectionTime.value;

        return true; // Allow form submission for "confirmPayment"
    }

    // If the "Cancel" button is clicked, no need to set collection date and time
    return true; // Allow form submission for "backToCart"
}
</script>
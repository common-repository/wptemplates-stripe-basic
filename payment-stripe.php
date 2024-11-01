<?php 
// Payment function
function wpts_payment_stripe() {
  global $wpdb;
  global $wpts_table_name;
  global $wpts_error_table_name;
  global $wpts_success_message;
  global $wpts_err_message;
  global $pagename;
  global $wpts_price_override;

  // Check if there are post values sent
  if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $wpts_id = ($_POST["wpts_id"]) ? $_POST["wpts_id"] : false;

    // Final amount will ONLY be used on oneoff payments
    $final_amount = ($_POST["final_amount"]) ? $_POST["final_amount"] : false;

    // Next check if this is the success page for Stripe
    $selectSQL = $wpdb->get_results("SELECT * FROM $wpts_table_name WHERE id='$wpts_id' LIMIT 1", ARRAY_A);
    // Count results
    $row_count = $wpdb->num_rows;
    // List results or show no buttons message.
    if($row_count) { 

      // If it is, carry on with the payment
      foreach ($selectSQL as $key => $value) {

        // Compare current page to success page
        if(strpos($value['button_success_url'], $pagename) !== false) {

          // Include the Stripe init file
          $init_stripe = plugin_dir_path( __FILE__ ) . 'stripe-php/init.php';
          // If you're using Composer, use Composer's autoload:
          require_once($init_stripe);

          $api_secret_key = (get_option('wpts_testmode') === 'on') ? get_option('wpts_test_secret') : get_option('wpts_live_secret');
          \Stripe\Stripe::setApiKey($api_secret_key);

          // Get the credit card details submitted by the form
          $token = $_POST['token_id_'.$value['button_custom_id']];

          // ONE OFF PAYMENT
          // ############################################################################
          // ############################################################################
          if($value['button_type'] === 'oneoff') {

            // Get the price from the submitted details - ONLY ON ONEOFF buttons
            $wpts_price = ($final_amount) ? $final_amount : $value['button_amount'];

            // Create the charge on Stripe's servers - this will charge the user's card
            try {

              $charge = \Stripe\Charge::create(array(
                "amount" => $wpts_price, // amount in cents, again
                "currency" => $value['button_currency'],
                "source" => $token,
                "description" => $value['button_description']
                ));

              $wpts_success_message = '<p><span style="font-weight:bold;">Amount:</span> '.number_format((floatval($wpts_price) / 100), 2, '.', ',').'<p>';


            } catch(\Stripe\Error\Card $e) {
              // The card has been declined

              $wpts_err_message = "<p>Unable to sign up customer: " . $_POST['token_email_'.$value['button_custom_id']].
                ", Error: " . $e->getMessage() . "</p>";  

              // Store the error message in the error table 
              $button_name = $value['button_name'];
              $insertSQL = "INSERT INTO $wpts_error_table_name (button_name, wpts_stripe_error_message) 
              VALUES ('$button_name','$wpts_err_message')";

              // Insert the string
              $sql_query = $wpdb->query($insertSQL);  

              // Go to fail page
              $wpts_fail_url = get_home_url() . '/' . $value['button_fail_url'];
              header('Location: '.$wpts_fail_url);
              exit;

            }

          // SUBSCRIPTION
          // ############################################################################
          // ############################################################################
          } else {
            try
              {

                try {
                  // Check if plan exists
                  $get_plan = \Stripe\Plan::retrieve($value['button_product_id']);

                  // If its all good then we dont need to do anything.
                }
                catch (Exception $e) {

                  // If there is an error, then we need to create the plan.
                  $plan = \Stripe\Plan::create(array(
                    "amount" => $value['button_amount'],
                    "interval" => $value['button_billing_cycle'],
                    "interval_count" => $value['button_billing_cycle_interval'],
                    "product" => array(
                      "name" => $value['button_product_name']
                    ),
                    "currency" => $value['button_currency'],
                    "id" => $value['button_product_id']
                  ));
                }

                $customer = \Stripe\Customer::create(array(
                  'email' => $_POST['token_email_'.$value['button_custom_id']],
                  'source'  => $token,
                  'plan' => $value['button_product_id']
                ));

                $wpts_success_message = "<p><span style='font-weight:bold;'>Amount:</span> ".number_format((floatval($value['button_amount']) / 100), 2, '.', ',')."<p>
                <p><span style='font-weight:bold;'>Billing Cycle: Every </span> ".$value['button_billing_cycle']."<p>
                <p><span style='font-weight:bold;'>Product Name:</span> ".$value['button_product_name']."<p>";

              }
            catch(Exception $e)
              {
                
                $wpts_err_message = "<p>Unable to sign up customer: " . $_POST['token_email_'.$value['button_custom_id']].
                ", Error: " . $e->getMessage() . "</p>";

                // Store the error message in the error table 
                $button_name = $value['button_name'];
                $insertSQL = "INSERT INTO $wpts_error_table_name (button_name, wpts_stripe_error_message) 
                VALUES ('$button_name','$wpts_err_message')";

                // Insert the string
                $sql_query = $wpdb->query($insertSQL);

                // Go to fail page
                $wpts_fail_url = get_home_url() . '/' . $value['button_fail_url'];
                header('Location: '.$wpts_fail_url);
                exit;
              }
          }
        } else {
          $wpts_success_message = "Page name DID NOT MATCH";
        }
      }
    }
  } else {
    $wpts_success_message = "No POST data";
  }
}
add_action('get_header', 'wpts_payment_stripe');

?>
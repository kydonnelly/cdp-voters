<?php
/**
 * Plugin Name: CDP Voters
 * Plugin URI: https://www.cooperative4thecommunity.com/cdp-voters
 * Description: A plugin to submit queries to the CDP Voter database and display results.
 * Version: 1.0
 * Author: Kyle Donnelly
 * Author URI: https://www.cooperative4thecommunity.com
 */

defined( 'ABSPATH' ) || exit;

define("USER_WILDCARD", '?');
# database column names, must match cdp_echo_results_html()
define("QUERY_COLUMNS", ['voter_id', 'first_name', 'middle_name', 'last_name', 'street_num', 'street', 'type', 'apt_num', 'zip', 'birth_date']);

function to_where_clause($input, $column) {
  if (empty($input) || $input == USER_WILDCARD) {
    // user is not filtering by this field
    return '';
  }

  // all values in the Alameda county voter database are capitalized.
  // could use COLLATE NOCASE or something if needed for future databases
  return "UPPER(" . $column . ") LIKE UPPER('" . str_replace(USER_WILDCARD, '%', $input) . "')";  
}

function cdp_echo_results_html($results) {
  if (empty($results)) {
    echo '<p>No results :(</p>';
    echo '<button name="invalid" onclick="invalidateSearch(this)">Cant Find</button>';
  } else {
    echo '<table style="width:100%">';
    foreach ($results as $result) {
      echo '<tr>';
      $middle = (strlen($result->middle_name) > 0 ? $result->middle_name . ' ' : '');
      $apt = (strlen($result->apt_num) > 0 ? ' #' . $result->apt_num : '');
      echo '<td><button class="blue-btn" name="validate" onclick="validateId(this)" id="' . $result->voter_id . '">Mark</button></td>';
      echo '<td>' . $result->first_name . ' ' . $middle . $result->last_name . '</td>';
      echo '<td>' . $result->street_num . ' ' . $result->street . ' ' . $result->type . $apt . '</td>'; 
      echo '<td>' . $result->zip . '</td>';
      echo '<td>' . $result->birth_date . '</td>';
      echo '</tr>';
    }
    echo '</table>';
    echo '<button name="invalid" onclick="invalidateSearch(this)">Mark Invalid</button>';
  }
}

function cdp_voter_lookup() {
  // ignore initial (or any) page load where the user didn't submit anything
  if (!isset( $_POST['submit'] )) {
    return;
  }

  $first_name = sanitize_text_field( $_POST['firstname'] );
  $last_name = sanitize_text_field( $_POST['lastname'] );
  $street_num = sanitize_text_field( $_POST['street_num'] );
  $street = sanitize_text_field( $_POST['street_name'] );
  $apt_num = sanitize_text_field( $_POST['apt_num'] ); 
  $zip = sanitize_text_field( $_POST['zip'] );

  // the database is not consistently specific eg 94610 vs 94610-3343, so always match both
  if ($zip != null) {
    $zip = $zip . USER_WILDCARD;
  }

  // get the list of fields they are filtering by
  $input_fields = [$first_name, $last_name, $street_num, $street, $apt_num, $zip];
  $input_columns = ['first_name', 'last_name', 'street_num', 'street', 'apt_num', 'zip'];
  $wheres = array_filter(array_map('to_where_clause', $input_fields, $input_columns));

  // no filters given; don't let them just look at the entire voter database list
  if (empty($wheres)) {
    echo '<p>Please provide search items.</p>';
    return;
  }
  $where_query = implode(' AND ', $wheres);

  global $wpdb;
  $table_name = $wpdb->prefix . "voters";
  $query = "SELECT " . implode(', ', QUERY_COLUMNS) . " from $table_name WHERE $where_query;";
  $results = $wpdb->get_results($query);

  cdp_echo_results_html($results);
}

function cdp_voter_form_code() {
  // Shows the input form, keeping any values from previous submission
  echo '<form action="" id="voter_form" method="post">';
  echo '<p>First Name: <input autocapitalize="off" spellcheck="false" autocorrect="off" type="text" name="firstname" id="first" value="' . $_POST['firstname'] . '" placeholder="Jane"><br />';
  echo 'Last Name: <input autocapitalize="off" spellcheck="false" autocorrect="off" type="text" name="lastname" id="last" value="' . $_POST['lastname'] . '" placeholder="Doe"><br />';
  echo 'Street Number: <input autocapitalize="off" spellcheck="false" autocorrect="off" type="text" name="street_num" id="snum" value="' . $_POST['street_num'] . '" placeholder="1428"><br />';
  echo 'Street Name: <input autocapitalize="off" spellcheck="false" autocorrect="off" type="text" name="street_name" id="street" value="' . $_POST['street_name'] . '" placeholder="Franklin"><br />';
  echo 'Apartment Number: <input type="text" name="apt_num" id="anum" value="' . $_POST['apt_num'] . '" placeholder="420"><br />';
  echo 'Zip Code: <input type="text" name="zip" id="zip" value="' . $_POST['zip'] . '" placeholder="94612"></p>';
  echo '<p><input type="submit" name="submit" id="submitButton" value="Submit">';
  echo '<input style="background-color:#c7c7c7" type="reset" name="clear" id="clearInput" value="Clear" onclick="return resetForm(this.form);"></p>';
  echo '</form>';
}

function cf_voters_shortcode() {
  // wordpress entry point
  ob_start();
  cdp_voter_form_code();
  cdp_voter_lookup();

  return ob_get_clean();
}

add_shortcode( 'cdp_voter_form', 'cf_voters_shortcode' );

?>

<?php
namespace SEORedirect301s;
if (!class_exists("TomM8")) {
  class TomM8 {
    // Creates a share website link for Facebook and Twitter.
    function add_social_share_links($url) {
      ?>
      <a title="Share On Facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo(esc_url($url)); ?>"><img style="width: 30px;" src="<?php echo(esc_url(get_option("siteurl"))); ?>/wp-content/plugins/wp-seo-redirect-301/images/facebook.jpg" style="width: 30px;" /></a>
      <a title="Share On Twitter" target="_blank" href="http://twitter.com/intent/tweet?url=<?php echo(esc_url($url)); ?>"><img style="width: 30px;" src="<?php echo(esc_url(get_option("siteurl"))); ?>/wp-content/plugins/wp-seo-redirect-301/images/twitter.jpg" style="width: 30px;" /></a>
      <a title="Rate it 5 Star" target="_blank" href="<?php echo(esc_html($url)); ?>"><img style="padding-bottom: 3px;" src="<?php echo(esc_url(get_option("siteurl"))); ?>/wp-content/plugins/wp-seo-redirect-301/images/rate-me.png" /></a>
      <?php
    }

    // Write content to a file.
    function write_to_file($write_content, $location) {
      $file = fopen($location, "w") or exit("Unable to open file!");
      $content = str_replace('\"', "\"", $write_content);
      $content = str_replace("\'", '\'', $content);
      fwrite($file, $content);
      fclose($file);
    }

    // Return current url.
    function get_current_url() {
      $pageURL = 'http';
      if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
      $pageURL .= "://";
      if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
      } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
      }
      return $pageURL;
    }

    // Inserts data into the database.  Returns true if inserted correct, false if not.
    function insert_record($table_name, $insert_array) {
      global $wpdb;
      ob_start();
      $wpdb->show_errors();
      $table_name_prefix = $wpdb->prefix.sanitize_text_field($table_name);
      $wpdb->insert($table_name_prefix, $insert_array);
      $wpdb->print_error();
      $errors = ob_get_contents();
      ob_end_clean();

      if (preg_match("/<strong>WordPress database error:<\/strong> \[\]/", $errors)) {
        return true;
      } else {
        $sql = "SHOW INDEXES FROM $table_name_prefix WHERE non_unique =0 AND Key_name !=  'PRIMARY'";
        $results = $wpdb->get_results($sql);
        foreach ($results as $result) {
          $col_name = $result->Column_name;
          if (preg_match("/Duplicate entry (.+)&#039;".$col_name."&#039;]/", $errors, $matches, PREG_OFFSET_CAPTURE)) {
            if (!preg_match("/Must have a unique value/", $_SESSION[$col_name."_error"])) {
              $_SESSION[$col_name."_error"] .= "Must have a unique value.";
            }

          }
        }
        return false;
      }
    }

    // Select records from the database. Returns sql results object.
    function get_results($table_name, $fields_array, $where_sql, $order_array = array(), $limit = "") {
      global $wpdb;
      $table_name_prefix = $wpdb->prefix.sanitize_text_field($table_name);
      if ($fields_array == "*") {
        $fields_comma_separated = "*";
      } else {
        $fields_comma_separated = sanitize_text_field(implode(",", $fields_array));
      }

      if (!empty($where_sql)) {
        $where_sql = "WHERE ".$where_sql;
      }
      $order_sql = "";
      if (!empty($order_array)) {
        $order_sql = "ORDER BY ".sanitize_text_field(implode(",", $order_array));
      }
      $limit_sql = "";
      if ($limit != "") {
        $limit_sql = "LIMIT ".sanitize_text_field($limit);
      }
      $sql = "SELECT $fields_comma_separated FROM $table_name_prefix $where_sql $order_sql $limit_sql";
      // echo $sql;
      return $wpdb->get_results($sql);
    }

    // Similar to get_row_by_id, but more flexibility with selecting the record that you want.
    function get_row($table_name, $fields_array, $where_sql) {
      global $wpdb;
      $table_name_prefix = $wpdb->prefix.sanitize_text_field($table_name);
      if ($fields_array == "*") {
        $fields_comma_separated = "*";
      } else {
        $fields_comma_separated = sanitize_text_field(implode(",", $fields_array));
      }
      return $wpdb->get_row("SELECT $fields_comma_separated FROM $table_name_prefix WHERE $where_sql LIMIT 1");
    }
  }
}

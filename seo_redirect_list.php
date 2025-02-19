<?php
namespace SEORedirect301s;
if ( ! defined( 'ABSPATH' ) ) exit;

if (isset($_GET["delete_id"])) {
	$nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce( $nonce, 'delete-slug-301-redirect'.sanitize_text_field($_GET["delete_id"]))) {
		die( __( 'Security check', 'Nonce is invalid' ) );
	} else {
		global $wpdb;
		$wpdb->delete($wpdb->prefix."slug_history", array("post_id" => sanitize_text_field($_GET["delete_id"]), "url" => sanitize_url($_GET["delete_url"])),  array('%d','%s'));
		admin_url("admin.php?page=wp-seo-redirect-301/seo_redirect_list.php", 200);
	}
}

$abcTom = new TomM8();
$my_redirects = $abcTom->get_results("slug_history", "*", "");

wp_enqueue_script('jquery');

?>

<h2>SEO Redirect 301</h2>
<div class="postbox " style="display: block; ">
<div class="inside">
	<p>A daily SEO 301 Redirect Sitemap will get generated daily at this url:</p>
	<p><a target="_blank" href="<?php echo(get_option("siteurl")); ?>/301-sitemap.xml"><?php echo(get_option("siteurl")); ?>/301-sitemap.xml</a></p>
	<p>Please submit this sitemap to <a href="https://www.google.com/webmasters/tools/home?hl=en" target="_blank">Google</a> and <a href="http://bing.com/webmaster/WebmasterManageSitesPage.aspx" target="_blank">Bing</a>.</p>
</div>
</div>

<div class="postbox " style="display: block; ">
<div class="inside">
		<table class="data">
			<tbody>	
			  <?php 
					$record_count = 0;
					foreach($my_redirects as $redirect) { ?>
			    <?php if ((get_permalink($redirect->post_id) != "") && (preg_replace("/\/$/", "", $redirect->url) != preg_replace("/\/$/", "", get_permalink($redirect->post_id)))) { 
						$record_count++;
						$nonce = wp_create_nonce( 'delete-slug-301-redirect'.$redirect->post_id );
						?>
				    <tr>
				      <td><a target="_blank" href="<?php echo($redirect->url); ?>"><?php echo($redirect->url); ?></a></td>
				      <td><strong style="margin: 0 10px;">redirects to</strong></td>
				      <td><a target="_blank" href="<?php echo(get_permalink($redirect->post_id)); ?>"><?php echo(get_permalink($redirect->post_id)); ?></a></td>
				      <td><a class="delete" href="<?php echo(get_option("siteurl")); ?>/wp-admin/admin.php?page=wp-seo-redirect-301/seo_redirect_list.php&delete_id=<?php echo($redirect->post_id); ?>&delete_url=<?php echo($redirect->url); ?>&_wpnonce=<?php echo($nonce); ?>">Delete</a></td>
				    </tr>
				  <?php } ?>
			  <?php } ?>			    
			</tbody>
			<?php if ($record_count == 0) { ?>
				<tfoot>
					<tr>
						<td colspan="4">You haven't changed any page/post slug names yet.</td>
					</tr>
				</tfoot>	
			<?php } ?>
		</table>
</div>
</div>

<?php $abcTom->add_social_share_links("http://wordpress.org/extend/plugins/wp-seo-redirect-301"); ?>

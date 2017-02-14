<?php
/* 
Plugin Name: Buildkar Brands Filter
Plugin URI: http://www.buildkar.com/
Description: Custom WooCommerce plugin for filtering products by brands. It uses Ultimate WooCommerce Brands plugin.
Version: 1.2.0
Author: Buildkar
*/
if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly
}
class buildkar_widget extends WP_Widget{
	
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'buildkar_widget',
			'description' => 'Brands filter widget',
		);
		parent::__construct( 'buildkar_widget', 'Brands Filter Widget', $widget_ops );
	}
	public function form($instance){
		if(isset($instance['title'])) {
			$title  = $instance['title'];
		}
		else {
			$title  = 'Shop by Brands';
		}

		if(!isset($instance['show_count'])) {
			$instance['show_count'] = 1;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label><?php echo __( 'Show product count (for Text brands display):', 'mgwoocommercebrands' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'show_count' ); ?>">
				<option value='0' <?php if ( $instance['show_count'] == 0 ) {	echo "selected='selected'"; } ?>><?php echo __( 'No', 'mgwoocommercebrands' ); ?></option>
				<option value='1' <?php if ( $instance['show_count'] == 1 ) {	echo "selected='selected'"; } ?>><?php echo __( 'Yes', 'mgwoocommercebrands' ); ?></option>
			</select>
		</p>
		<?php
	}
	public function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['show_count'] = esc_sql( $new_instance['show_count'] );
		$instance['title']      = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}
	public function widget($args ,$instance){
		extract( $args );
		$show_count = $instance['show_count'];
		if(is_tax('product_cat') || is_search()){

		if ( $instance['title'] ) {
			
			?>
			<li class="widget-container brands-widget">
			<div class="widget_head_txt">
			<?php echo "<h3>{$instance['title']}</h3>"; ?>
			</div>
			<?php
		}
		global $wpdb;
		$term_id_array=array();
		$cur_cat_name=single_cat_title("", false);
		$searched_item=get_search_query();
		$search_item_new=strtoupper(ucwords($searched_item));
		
		//echo $search_item_new;
		$brands_list1 = get_terms( 'product_cat', array(
			'orderby'   => 'name',
			'order'     => 'ASC',
			'fields'	=> 'all',
		));
		foreach($brands_list1 as $brands1){
			
			if($brands1->name==$cur_cat_name || strtoupper($brands1->name)==$search_item_new){
				$parent_res=$wpdb->get_col("SELECT DISTINCT {$wpdb->terms}.name FROM {$wpdb->terms}
				INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
				INNER JOIN {$wpdb->term_relationships} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
				WHERE {$wpdb->term_taxonomy}.taxonomy = 'product_brand' AND {$wpdb->term_relationships}.object_id IN (
				SELECT object_id FROM {$wpdb->term_relationships}
				INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
				WHERE {$wpdb->term_taxonomy}.term_id = '$brands1->term_id'
				);");
				foreach($parent_res as $res){
					array_push($term_id_array, $res);
				}
				$child_terms=get_term_children( $brands1->term_id, 'product_cat' );
				foreach($child_terms as $child1){
			
					$result_ar=$wpdb->get_col("SELECT DISTINCT {$wpdb->terms}.name FROM {$wpdb->terms}
					INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
					INNER JOIN {$wpdb->term_relationships} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
					WHERE {$wpdb->term_taxonomy}.taxonomy = 'product_brand' AND {$wpdb->term_relationships}.object_id IN (
					SELECT object_id FROM {$wpdb->term_relationships}
					INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
					WHERE {$wpdb->term_taxonomy}.term_id = '$child1'
					);");
					foreach($result_ar as $resulta){
						array_push($term_id_array, $resulta);
					}
				}
		
			}
		}
		$terms_id_array=array_unique($term_id_array);
		
		$brands_list = get_terms( 'product_brand', array(
			'orderby'   => 'name',
			'order'     => 'ASC',
		));
		echo '<form action="" id="buildkar_form">';
		echo '<div class="widget woocommerce widget_mgwoocommercebrands fltr_dv">';

		echo '<input type="text" class="brands_search" placeholder="Search Brands">';
		if ( !empty( $brands_list ) && !is_wp_error( $brands_list ) ){
			
			echo '<ul class="checkbox_cover">';

			foreach ( $brands_list as $brand_item ) {
				foreach($terms_id_array as $items_t){
				if($brand_item->name==$items_t){
				echo '<li class="checkbox_dv">';
				if($show_count == 1) {
					echo '<input type="checkbox" name="'.$brand_item->name.'" id="id'.$brand_item->term_id.'" value="'.$brand_item->slug.'"><label for="id'.$brand_item->term_id.'">'.$brand_item->name.'<span class="count">('.$brand_item->count.')</span></label>';
				} else {
					echo '<input type="checkbox" name="'.$brand_item->name.'" id="id'.$brand_item->term_id.'" value="'.$brand_item->slug.'"><label for="id'.$brand_item->term_id.'">'.$brand_item->name.'</label>';
				}
				echo '</li>';
			}
			}
			}
			echo '</ul>';
		}
		?>
		</div>
		<div class="widget_btn_cover">
		<input type="reset" value="Reset" class="buttn reset_buttn" />
		<span class="buttn more_buttn"></span>
		</div>
		</form>
		</li>
		<?php
	}
	}
}

// Register and load the widget
function brand_load_widget() {
	register_widget( 'buildkar_widget' );
}
function brands_enqueue(){
	if(is_tax('product_cat') || is_search()){
		//wp_enqueue_style('styles');
		//wp_enqueue_script('wp_add_script' , $in_footer = true);	
	}
}
function brand_process_widget(){
	//wp_register_style('styles', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/styles.css', '1.2.0');
	//wp_register_script('wp_add_script' , plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/wp_add_script.js', array('jquery'), '1.2.0');
	//wp_register_script('wp_add_script', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/wp_add_script.js', array('jquery') , $in_footer = true);
	//wp_localize_script( 'wp_add_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )  , $in_footer = true);
}

add_action( 'widgets_init', 'brand_load_widget' );
add_action( 'widgets_init', 'brand_process_widget' );
add_action( 'wp_enqueue_scripts', 'brands_enqueue' );

add_action( 'wp_ajax_buildkar_brand_action', 'buildkar_brand_action' );
add_action( 'wp_ajax_nopriv_buildkar_brand_action', 'buildkar_brand_action' );

//Callback function
function buildkar_brand_action(){
?>
<ul class="product">
<?php
 if ( isset($_POST['termId']) && ! empty($_POST['termId']) ) {
$terms_id = $_POST[ 'termId' ];
foreach($terms_id as $term_id){
$args = array (
'term' => $term_id,
'order' => 'DESC',
	'tax_query' => array(
	  array(
		  'taxonomy' => 'product_brand',
		  'field'    => 'slug',
		  'terms'    => $term_id,
		  'operator' => 'IN'
		  )
	  ) 
 );
$new_query = new WP_Query( $args );
if ( $new_query->have_posts() ) {
	while ( $new_query->have_posts() ) : $new_query->the_post();
		wc_get_template_part( 'content', 'product' );
	endwhile;
}
wp_reset_postdata();
}
 }
die();
?>
</ul>
<?php
}
?>
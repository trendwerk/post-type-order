<?php
/**
 * Plugin Name: Post type order
 * Description: Order posts in any post type.
 *
 * Plugin URI: https://github.com/trendwerk/post-type-order
 *
 * Author: Trendwerk
 * Author URI: https://github.com/trendwerk
 *
 * Version: 2.0.0
 */
namespace Trendwerk\PostTypeOrder;

class PostTypeOrder
{
	var $post_types = array();
	var $taxonomies = array();

	function __construct()
	{
		add_action('plugins_loaded', array($this, 'localization'));
		add_action('init',array($this,'init'),11);
		add_action('admin_menu',array($this,'addMenu'));
		add_action('admin_enqueue_scripts',array($this,'addScripts'));
		add_action('wp_ajax_tp_pt_order',array($this,'order'));
	}

	/**
	 * Load localization
	 */
	function localization()
	{
		load_muplugin_textdomain('pt-order', dirname(plugin_basename(__FILE__)) . '/assets/lang/');
	}

	/**
	 * Scripts
	 */
	function addScripts()
	{
		wp_enqueue_script('tp-pt-order', plugins_url('assets/js/admin.js', __FILE__), array('jquery', 'jquery-ui-sortable'));
		wp_enqueue_style('tp-pt-order', plugins_url('assets/sass/admin.css', __FILE__));
	}

	/**
	 * Init which post types want to be ordered
	 */
	function init()
	{
		$post_types = get_post_types();
		if($post_types){
			foreach($post_types as $post_type){
				if(!post_type_supports($post_type,'order')) {
					continue;
				}

				$post_type = get_post_type_object($post_type);
				$this->post_types[] = $post_type->name;

				if(isset($post_type->order_by_taxonomy)){
					$this->taxonomies[$post_type->name] = (string) $post_type->order_by_taxonomy;
				}
			}
		}
	}

	/**
	 * Add order menu
	 */
	function addMenu()
	{
		if($this->post_types){
			foreach($this->post_types as $post_type){
				add_submenu_page('edit.php?post_type='.$post_type,__('Order','pt-order'),__('Order','pt-order'),'edit_posts','order-'.$post_type,array($this,'manage'));
			}
		}
	}

	/**
	 * Manage order
	 */
	function manage()
	{
		$post_type = $_GET['post_type'];

		if(isset($this->taxonomies[$post_type])){
			$taxonomy = $this->taxonomies[$post_type];
			$terms = get_terms($taxonomy);
		}
		?>

		<div class="wrap">
			<div class="icon32" id="icon-page"><br /></div>
			<h2><?php _e('Order', 'pt-order'); ?></h2>

			<?php
				if(!isset($terms)) {
				/**
				 * Normal sorting
				 */
			?>
				<div class="tp-pt-order">
					<?php $this->displayPosts(array('post_type' => $post_type,'post_parent' => 0)); ?>
				</div>
			<?php
				} else {
				/**
				 * Sort by taxonomy
				 */
			?>
				<div class="tp-pt-order" data-taxonomy="<?php echo $taxonomy; ?>">
					<?php foreach($terms as $term){ ?>
						<div class="tp-pt-order-taxonomy" data-term="<?php echo $term->term_id; ?>">
							<h3><?php echo $term->name; ?></h3>
							<?php $this->displayPosts(array('post_type' => $post_type, 'post__in' => $this->getPosts($term,$taxonomy,$post_type), 'orderby' => 'post__in')); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>

		<?php
	}

	/**
	 * Display posts as list
	 *
	 * @param array $args Additional arguments
	 */
	function displayPosts($args)
	{
		$defaults = array(
			'posts_per_page' => -1
		);
		$args = wp_parse_args($args,$defaults);

		$posts = new WP_Query($args);
		if($posts->have_posts()){
			?>

			<ul>
				<?php
					while($posts->have_posts()) {
						$posts->the_post();
						global $post;
						?>

						<li id="<?php the_ID(); ?>">
							<?php the_title(); ?>

							<?php _post_states($post); ?>

							<?php
								$this->displayPosts(wp_parse_args(array(
									'post_parent' => get_the_ID()
								),$args));
							?>
						</li>

						<?php
					}
				?>
			</ul>

			<?php
		}
		wp_reset_query();
	}

	/**
	 * @abstract Get (ordered) post IDs from a taxonomy
	 *
	 * @param object $term
	 * @param string $taxonomy
	 * @param string $post_type
	 */
	function getPosts($term, $taxonomy, $post_type)
	{
		$pt_order = get_option('tp-pt-order');

		/**
		 * Get all posts from this term (in case some haven't been saved to the order yet)
		 */
		$all_posts = getPosts(array(
			'post_type'   => $post_type,
			'post_parent' => 0,
			'numberposts' => -1,
			$taxonomy     => $term->slug
		));

		/**
		 * Add posts at the bottom that aren't ordered yet
		 */
		$all_posts_ids = array();

		if($all_posts){
			foreach($all_posts as $post){
				$all_posts_ids[] = $post->ID;

				if(!is_array($pt_order[$term->term_id])){
					$pt_order[$term->term_id] = array();
				}

				if(!in_array($post->ID,$pt_order[$term->term_id])){
					$pt_order[$term->term_id][] = $post->ID;
				}
			}
		}

		/**
		 * Remove posts from $pt_order that are actually removed already
		 */
		if(is_array($pt_order[$term->term_id])){
			foreach($pt_order[$term->term_id] as $key=>$term_id){
				if(!in_array($term_id,$all_posts_ids)) unset($pt_order[$term->term_id][$key]);
			}
		}

		/**
		 * Return array of ordered post IDs and save new posts at bottom
		 */
		update_option('tp-pt-order',$pt_order);

		return $pt_order[$term->term_id];
	}

	/**
	 * Order through AJAX
	 */
	function order()
	{
		remove_all_actions('save_post');

		$order = $_POST['order'];

		if(is_array($order)){
			$i=1;
			foreach($order as $post_id){
				if(!$_POST['taxonomy']){
					$post = array();
					$post['ID'] = $post_id;
					$post['menu_order'] = $i;

					wp_update_post($post);
				} else {
					/**
					 * Order by taxonomy
					 */
					$pt_order = get_option('tp-pt-order');
					$pt_order[$_POST['term']] = $_POST['order'];
					update_option('tp-pt-order',$pt_order);
				}

				$i++;
			}
		}

		die();
	}

} new PostTypeOrder;

Post type order
===============

Order posts in any post type. Made for WordPress.

## Features

- Allows admins to order posts from any post type in a separate "Order" menu
- Automatically orders posts
- Divide ordering by taxonomy

## Usage

### Step 1
Add the post type support 'order' to any post type
	
	'supports' => array( 'title', 'editor', 'revisions', 'order' )

### Step 2 (optional)
You can divide the post type ordering by taxonomy

	'supports'          => array( 'title', 'editor', 'revisions', 'order' ),
	'order_by_taxonomy' => $taxonomy

If you want to use this, it's impossible to do automatic ordering. You will need to adjust the query manually.
Below is an example of a custom loop. This is a little more complex, but I'm sure you'll figure it out.

	$posts = new WP_Query( array(
		'post_type' => $post_type,
		'post__in'  => TP_Post_Type_Order::get_posts( $term, $taxonomy, $post_type ),
		'orderby'   => 'post__in'
	) );

### Step 3
Order up!
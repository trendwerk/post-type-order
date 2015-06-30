Post type order
===============

Order posts in any post type. Made for WordPress.

## Features

- Allows admins to order posts from any post type in a separate "Order" menu
- Automatically orders posts
- Divide ordering by taxonomy

## Installation
If you're using Composer to manage WordPress, add this plugin to your project's dependencies. Run:
```sh
composer require trendwerk/post-type-order 2.0.0
```

Or manually add it to your `composer.json`:
```json
"require": {
	"trendwerk/post-type-order": "2.0.0"
},
```

## Usage

### Step 1
Add the post type support 'order' to any post type

	'supports' => array( 'title', 'editor', 'revisions', 'order' )

### Step 2
Add 'orderby' and 'order' arguments to your custom loop.

```php
$args = array(
	'post_type' => $post_type,
	'orderby'   => 'menu_order',
	'order'     => 'ASC'
);
$query = new WP_Query( $args );
```

Or set the 'orderby' and 'order' arguments globally. You probably want to use some conditional statements to only set these arguments on particular pages. For example:

```php
$query->set('orderby', 'menu_order');
$query->set('order', 'ASC');
```

### Step 3 (optional)
You can divide the post type ordering by taxonomy

```php
'supports'          => array( 'title', 'editor', 'revisions', 'order' ),
'order_by_taxonomy' => $taxonomy
```

If you want to use this, it's impossible to do automatic ordering. You will need to adjust the query manually.
Below is an example of a custom loop. This is a little more complex, but I'm sure you'll figure it out.

```php
$posts = new WP_Query( array(
	'post_type' => $post_type,
	'post__in'  => TP_Post_Type_Order::get_posts( $term, $taxonomy, $post_type ),
	'orderby'   => 'post__in'
) );
```

### Step 4
Order up!

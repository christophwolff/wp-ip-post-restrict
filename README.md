# IP Post Restrict



Adds a Meta Checkbox to the posteditor to hide the post.

Check with ipr_is_post_restricted(get_the_id()`, i.e.

```php

<?php if (ipr_is_post_restricted(get_the_id())){

		echo "Access denied!"

	} else {

		echo "Access granted!"

	}
?>

```

Restrict posts to IPs or IP ranges. Can be configured in the WordPress Network Settings. 

Check a clients IP with `ipr_client_ip_is_allowed()`, for example:

```php
<?php
if (ipr_client_ip_is_allowed()) {
	echo "You are allowed to see this.";
} else {
	echo "Nothing to see here, please move along.";
}
?>
```

Warning: The client IP can be spoofed. Do not hide secure information using this technique.



Bringing it together i.e. in a loop:

```php
<?php

while ( $index_query->have_posts() ) : $index_query->the_post(); ?>


	if (!ipr_is_post_restricted(get_the_id()) && !ipr_client_ip_is_allowed()){

			get_template_part( 'template-parts/content', 'post' );

	}

<?php endwhile; ?>

?>

```

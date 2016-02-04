# IP Post Restrict

Restrict posts to IPs or IP ranges. Can be configured in the WordPress Network Settings.

Check a clients IP with `ipr_client_ip_is_allowed()`, for example:

```php
<?php
if (ipr_client_ip_is_allowed()) {
	echo "You are allowed to see this.";
} else {
	echo "Nothing to see here, please move along."
}
?>
```

Warning: The client IP can be spoofed. Do not hide secure information using this technique.

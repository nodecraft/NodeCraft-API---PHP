NodeCraft-API---PHP
===================

PHP Library for the Official [NodeCraft API](http://developers.nodecraft.com).


Code Example
----
Please review the [API Documentation](http://developers.nodecraft.com) for more details on specific operations and acquiring an API key.
```
<?php
	require_once('nodecraft-api.class.php');

	$api = new nodecraftAPI('username', 'xxxxx-xxxxx-xxxxx-xxxxx');
	$results = $api->servicesList();
?>
```

Documentation
----
* [Services (Game Servers)](https://github.com/nodecraft/NodeCraft-API---PHP/wiki/Services-(Game-Servers))
* [Co-Op Vault](https://github.com/nodecraft/NodeCraft-API---PHP/wiki/Co-Op-Vault)

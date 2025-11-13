Case Insensitive YOURLS
Makes YOURLS case insensitive

Example use cases:

1. Users create short links with mixed cases (ex. ShrtLnk) and then they forget or someone makes a mistake (ex. shrtLnk).  This plugin allows users to make such mistakes (ex. ShrtLnk, shrtLnk, Shrtlnk, shrtlnk, etc. all work the same).
2. Users might decide to create very similar short links (ex. user 1 creates ShrtLnk and user 2 creates shrtlnk).  This can be confusing.  This plugin eliminates such issues (ex. if user 2 tries to create shrtlnk and ShrtLnk already exists then the short link creation fails).

Instructions:

1. Copy the 'case-insensitive' folder to user/plugins/.
2. Activate the plugin in the YOURLS admin interface.

That's it.

Note: This plugin assumes that the value of YOURLS_URL_CONVERT in your config.php file is 62.  If it's 36 then this plugin is both unecessary and inapplicable since YOURLS will anyway only accept lower case characters.

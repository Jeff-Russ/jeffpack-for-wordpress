# JeffPack for WordPress

Jeffpack is a single Plugin/Theme pair for WordPress which actually encompasses a large, modular set of plugins and themes. In other words, the single theme is actually a wrapper around multiple, selectable themes and the single plugin is actually many, also selectable. 

## jeffpack-core

JeffPack for WordPress itself refers these sets of options and is built upon jeffpack-core, which would is a reusable library for creating different projects. The idea behind JeffPack for WordPress and jeffpack-core is to have single object from the class JeffPackCore that is shared by both the master plugin and the master theme. The "master" manages the toggling of each subtheme and subplugin itself, bypassing the WordPress API's mechanisms for doing this. One should consider the "subplugins" as simply "plugins" and "subthemes" as "themes" wherein the "master" is merely a container. 

[API Documentation for Wp-php](https://cdn.rawgit.com/Jeff-Russ/jeffpack-for-wordpress/master/Jr-php/phpdoc/index.html)

[API Documentation for Wp-wp](https://cdn.rawgit.com/Jeff-Russ/jeffpack-for-wordpress/master/Jr-wp/phpdoc/index.html)
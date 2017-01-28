<?php

###### Jr-php/ ################################################################

include_once 'Jr-php/functions.php';
include_once 'Jr-php/TPersistArgs.php';

# Jr-php/dependencies
if (! class_exists('scssc', false)) {
    if (version_compare(PHP_VERSION, '5.4') < 0) {
        throw new \Exception('scssphp requires PHP 5.4 or above');
    }
    include_once 'Jr-php/dependencies/scssphp/src/Base/Range.php';
    include_once 'Jr-php/dependencies/scssphp/src/Block.php';
    include_once 'Jr-php/dependencies/scssphp/src/Colors.php';
    include_once 'Jr-php/dependencies/scssphp/src/Compiler.php';
    include_once 'Jr-php/dependencies/scssphp/src/Compiler/Environment.php';
    include_once 'Jr-php/dependencies/scssphp/src/Exception/CompilerException.php';
    include_once 'Jr-php/dependencies/scssphp/src/Exception/ParserException.php';
    include_once 'Jr-php/dependencies/scssphp/src/Exception/ServerException.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/Compact.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/Compressed.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/Crunched.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/Debug.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/Expanded.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/Nested.php';
    include_once 'Jr-php/dependencies/scssphp/src/Formatter/OutputBlock.php';
    include_once 'Jr-php/dependencies/scssphp/src/Node.php';
    include_once 'Jr-php/dependencies/scssphp/src/Node/Number.php';
    include_once 'Jr-php/dependencies/scssphp/src/Parser.php';
    include_once 'Jr-php/dependencies/scssphp/src/Type.php';
    include_once 'Jr-php/dependencies/scssphp/src/Util.php';
    include_once 'Jr-php/dependencies/scssphp/src/Version.php';
    include_once 'Jr-php/dependencies/scssphp/src/Server.php';
}

###### Jr-wp/ ################################################################

include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/file.php';
include_once substr( __FILE__, 0, strpos(__FILE__, "wp-content") ).'wp-admin/includes/plugin.php';

include_once 'Jr-wp/WpInfoHash.php';
include_once 'Jr-wp/WpSettingsHelper.php';

include_once 'Jr-wp/WpScssSettings.php';
include_once 'Jr-wp/Jeffpack.php';


include_once 'Jr-wp/WpScss.php';
// include_once 'Jr-wp/wp-scss.php';

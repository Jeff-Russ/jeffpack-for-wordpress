<?php
if (version_compare(PHP_VERSION, '5.4') < 0) {
    throw new \Exception('scssphp requires PHP 5.4 or above');
}

if (! class_exists('scssc', false)) {
    include_once __DIR__ . '/../../../bin/scssphp/src/Base/Range.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Block.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Colors.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Compiler.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Compiler/Environment.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Exception/CompilerException.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Exception/ParserException.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Exception/ServerException.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/Compact.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/Compressed.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/Crunched.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/Debug.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/Expanded.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/Nested.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Formatter/OutputBlock.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Node.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Node/Number.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Parser.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Type.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Util.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Version.php';
    include_once __DIR__ . '/../../../bin/scssphp/src/Server.php';
}

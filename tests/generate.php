<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

use Spiral\Tokenizer;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);

//Composer
require dirname(__DIR__) . '/vendor/autoload.php';

$tokenizer = new Tokenizer\Tokenizer(new Tokenizer\Config\TokenizerConfig([
    'directories' => [__DIR__],
    'exclude'     => []
]));

$databases = [
    'sqlite'    => [
        'namespace' => 'Cycle\Schema\Tests\Driver\SQLite',
        'directory' => __DIR__ . '/Annotated/Driver/SQLite/'
    ],
    'mysql'     => [
        'namespace' => 'Cycle\Schema\Tests\Driver\MySQL',
        'directory' => __DIR__ . '/Annotated/Driver/MySQL/'
    ],
    'postgres'  => [
        'namespace' => 'Cycle\Schema\Tests\Driver\Postgres',
        'directory' => __DIR__ . '/Annotated/Driver/Postgres/'
    ],
    'sqlserver' => [
        'namespace' => 'Cycle\Schema\Tests\Driver\SQLServer',
        'directory' => __DIR__ . '/Annotated/Driver/SQLServer/'
    ]
];

echo "Generating test classes for all database types...\n";

$classes = $tokenizer->classLocator()->getClasses(\Cycle\Schema\Tests\BaseTest::class);

foreach ($classes as $class) {
    if (!$class->isAbstract() || $class->getName() == \Cycle\Schema\Tests\BaseTest::class) {
        continue;
    }

    echo "Found {$class->getName()}\n";
    foreach ($databases as $driver => $details) {
        $filename = sprintf("%s/%s.php", $details['directory'], $class->getShortName());

        file_put_contents(
            $filename,
            sprintf(
                '<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
 
namespace %s; 

class %s extends \%s 
{ 
    const DRIVER = "%s"; 
}',
                $details['namespace'],
                $class->getShortName(),
                $class->getName(),
                $driver
            )
        );
    }
}

// helper to validate the selection results
// file_put_contents('out.php', '<?php ' . var_export($selector->fetchData(), true));
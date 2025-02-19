<?php

namespace KitLoong\MigrationsGenerator\Tests\Feature\MariaDB;

use Illuminate\Support\Facades\Schema;
use KitLoong\MigrationsGenerator\Tests\Feature\FeatureTestCase;
use PDO;

abstract class MariaDBTestCase extends FeatureTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'mariadb');
        $app['config']->set('database.connections.mariadb', [
            'driver'         => 'mysql',
            'url'            => null,
            'host'           => env('MARIADB_HOST'),
            'port'           => env('MARIADB_PORT'),
            'database'       => env('MARIADB_DATABASE'),
            'username'       => env('MARIADB_USERNAME'),
            'password'       => env('MARIADB_PASSWORD'),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_general_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'engine'         => null,
            'options'        => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);
    }

    protected function dumpSchemaAs(string $destination): void
    {
        $password = (!empty(config('database.connections.mariadb.password')) ?
            '-p\'' . config('database.connections.mariadb.password') . '\'' :
            '');

        $skipColumnStatistics = '';
        if (env('MYSQLDUMP_HAS_OPTION_SKIP_COLUMN_STATISTICS')) {
            $skipColumnStatistics = '--skip-column-statistics';
        }

        $command = sprintf(
            'mysqldump -h %s -P %s -u %s ' . $password . ' %s --compact --no-data ' . $skipColumnStatistics . ' > %s',
            config('database.connections.mariadb.host'),
            config('database.connections.mariadb.port'),
            config('database.connections.mariadb.username'),
            config('database.connections.mariadb.database'),
            $destination
        );
        exec($command);
    }

    protected function dropAllTables(): void
    {
        Schema::dropAllViews();
        Schema::dropAllTables();
    }
}

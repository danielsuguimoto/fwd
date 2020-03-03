<?php

namespace Tests\Feature;

use Tests\TestCase;

class MysqlRawTest extends TestCase
{
    public function testMysql()
    {
        $this->artisan('mysql-raw')->assertExitCode(0);

        $this->assertDockerComposeExec("-e MYSQL_PWD='secret' database mysql -u root");
    }

    public function testMysqlExecution()
    {
        $this->artisan('mysql-raw docker -e "show databases"')->assertExitCode(0);

        $this->assertDockerComposeExec("-e MYSQL_PWD='secret' database mysql -u root docker -e 'show databases'");
    }
}

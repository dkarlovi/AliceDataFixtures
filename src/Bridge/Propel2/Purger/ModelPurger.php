<?php

/*
 * This file is part of the Fidry\AliceDataFixtures package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fidry\AliceDataFixtures\Bridge\Propel2\Purger;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Fidry\AliceDataFixtures\Persistence\PurgerFactoryInterface;
use Fidry\AliceDataFixtures\Persistence\PurgerInterface;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
use Nelmio\Alice\IsAServiceTrait;
use Propel\Runtime\Propel;

/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @final
 */
/*final*/ class ModelPurger implements PurgerInterface, PurgerFactoryInterface
{
    use IsAServiceTrait;

    /**
     * @var string
     */
    private $generatedSqlPath;

    public function __construct(string $generatedSqlPath)
    {
        $this->generatedSqlPath = $generatedSqlPath;
    }

    /**
     * @inheritdoc
     */
    public function create(PurgeMode $mode, PurgerInterface $purger = null): PurgerInterface
    {
        return new self();
    }

    /**
     * @inheritdoc
     */
    public function purge()
    {
        $connection = Propel::getConnection();
        $sqlPath = sprintf('%s/%s.sql', $this->generatedSqlPath, $connection->getName());

        if (false === file_exists($sqlPath)) {
            throw new \RuntimeException(sprintf(
                'No propel generated SQL file exists at "%s", do you need to generate it?',
                $sqlPath
            ));
        }

        $connection->exec(file_get_contents($sqlPath));
    }
}

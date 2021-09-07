<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace MASK\Mask\Updates;

use MASK\Mask\Definition\TableDefinitionCollection;
use MASK\Mask\Loader\LoaderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MoveRteOptions implements UpgradeWizardInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    public function __construct()
    {
        $this->loader = GeneralUtility::makeInstance('mask.loader');
    }

    public function getIdentifier(): string
    {
        return 'moveRteOptions';
    }

    public function getTitle(): string
    {
        return 'Update Mask JSON file (RTE options)';
    }

    public function getDescription(): string
    {
        return 'This update moves the option "rte" from the elements option to tca as "enableRichtext".';
    }

    public function executeUpdate(): bool
    {
        $tableDefinitionCollection = $this->loader->load();
        $tableDefinitionArray = $tableDefinitionCollection->toArray();
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->elements as $element) {
                foreach ($element->options as $index => $option) {
                    if ($option === 'rte') {
                        unset($tableDefinitionArray[$tableDefinition->table]['elements'][$element->key]['options'][$index]);
                    }
                }
            }
        }
        $this->loader->write(TableDefinitionCollection::createFromArray($tableDefinitionArray));
        return true;
    }

    public function updateNecessary(): bool
    {
        $tableDefinitionCollection = $this->loader->load();
        foreach ($tableDefinitionCollection as $tableDefinition) {
            foreach ($tableDefinition->elements as $element) {
                foreach ($element->options as $option) {
                    if ($option === 'rte') {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}

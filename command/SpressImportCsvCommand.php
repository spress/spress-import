<?php

use Spress\Import\ProviderCollection;
use Spress\Import\ProviderManager;
use Spress\Import\Provider\CsvProvider;
use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Plugin\CommandDefinition;
use Yosymfony\Spress\Plugin\CommandPlugin;

class SpressImportCsvCommand extends CommandPlugin
{
    /**
     * Gets the command's definition.
     *
     * @return \Yosymfony\Spress\Plugin\CommandDefinition Definition of the command.
     */
    public function getCommandDefinition()
    {
        $definition = new CommandDefinition('import:csv');
        $definition->setDescription('Import a blog from CSV files');
        $definition->setHelp('Import command for CSV files');

        $definition->addArgument('file', CommandDefinition::REQUIRED, 'Path to CSV file');
        $definition->addOption('dry-run', null, null);
        $definition->addOption('post-layout', null, CommandDefinition::VALUE_REQUIRED, 'Layout for post items');
        $definition->addOption('not-replace-urls', null, null, 'Do not replace old source URLs to the new Spress path');
        $definition->addOption('not-header', null, null);
        $definition->addOption('delimiter-character', null, CommandDefinition::VALUE_REQUIRED, 'Delimited character', ',');
        $definition->addOption('enclosure-character', null, CommandDefinition::VALUE_REQUIRED, 'Enclousure character', '"');
        $definition->addOption('terms-delimiter-character', null, CommandDefinition::VALUE_REQUIRED, 'Terms delimiter character. Used in categories and tags columns', ';');

        return $definition;
    }

    /**
     * Executes the current command.
     *
     * @param \Yosymfony\Spress\Core\IO\IOInterface $io        Input/output interface.
     * @param array                                 $arguments Arguments passed to the command.
     * @param array                                 $options   Options passed to the command.
     *
     * @return null|int null or 0 if everything went fine, or an error code.
     */
    public function executeCommand(IOInterface $io, array $arguments, array $options)
    {
        $header = false;
        $style = new SpressImportConsoleStyle($io);
        $file = $arguments['file'];
        $srcPath = __DIR__.'/../../../../';

        $style->title('Importing from a CSV file');

        $providerCollection = new ProviderCollection([
            'csv' => new CsvProvider(),
        ]);
        $providerManager = new ProviderManager($providerCollection, $srcPath);

        if ($options['dry-run'] == true) {
            $providerManager->enableDryRun();

            $io->write('<info>Dry-run enabled</info>');
        }

        if ($options['not-header'] == true) {
            $header = true;
            $io->write('<info>CSV file without header</info>');
        }

        if ($options['not-replace-urls'] == true) {
            $providerManager->doNotReplaceUrls();
        }

        if (is_null($options['post-layout']) == false) {
            $providerManager->setPostLayout($options['post-layout']);
        }

        $io->write('<info>Starting...</info>');

        try {
            $itemResults = $providerManager->import('csv', [
                'file' => $file,
                'not_header' => $header,
                'delimiter_character' => $options['delimiter-character'],
                'enclosure_character' => $options['enclosure-character'],
                'terms_delimiter_character' => $options['terms-delimiter-character'],
            ]);

            $style->ResultItems($itemResults);
        } catch (Exception $e) {
            $style->error($e->getMessage());
        }
    }

    /**
     * Gets the metas of a plugin.
     *
     * Standard metas:
     *   - name: (string) The name of plugin.
     *   - description: (string) A short description of the plugin.
     *   - author: (string) The author of the plugin.
     *   - license: (string) The license of the plugin.
     *
     * @return array
     */
    public function getMetas()
    {
        return [
            'name' => 'spress/spress-import-csv',
            'description' => 'A command for importing from CSV files',
            'author' => 'Victor Puertas',
            'license' => 'MIT',
        ];
    }
}

<?php

use Yosymfony\Spress\Core\IO\IOInterface;

/**
 * Console styles for import commands.
 */
class SpressImportConsoleStyle
{
    protected $io;

    /**
     * Constructor.
     *
     * @param     IOInterface IO operations. You know: write, ask...
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * Writes a title.
     * Example of output:
     * ```
     * The title
     * ---------
     * ```.
     *
     * @param string $message The title.
     */
    public function title($message)
    {
        $this->io->write([
            '',
            sprintf('<comment>%s</>', $message),
            sprintf('<comment>%s</>', str_repeat('=', strlen($message))),
            '',
        ]);
    }

    /**
     * Writes a section.
     * Example of output:
     * ```
     * The section
     * -----------
     * ```.
     *
     * @param string $message The message.
     */
    public function section($message)
    {
        $this->io->write([
            '',
            sprintf('<comment>%s</>', $message),
            sprintf('<comment>%s</>', str_repeat('-', strlen($message))),
            '',
        ]);
    }

    /**
     * Writes an error.
     * Example of output:
     * ```
     * Error:
     *  The error message
     * ```.
     */
    public function error($message)
    {
        $this->io->write([
            '',
            sprintf('<error>Error: %s</error>', $message),
            '',
        ]);
    }

    /**
     * Writes a list of ResultItem objects.
     *
     * @param Spress\Import\ResultItem[] List of ResultItem.
     */
    public function ResultItems(array $resultItems)
    {
        $errors = 0;
        $success = 0;

        $this->io->write([
            '',
            'Imported items:',
        ]);

        foreach ($resultItems as $item) {
            $source = $item->getSourcePermalink();
            $destination = $item->getRelativePath();
            $newOrReplace = $item->existsFilePreviously() ? 'DW' : 'W';
            $this->io->write(sprintf(' * <info>[%s]</info> %s <comment>-></comment> %s', $newOrReplace, $source, $destination));

            if (empty($item->getMessage()) == false) {
                $this->io->write(sprintf('  <error>Message: %s</error>', $item->getMessage()));
            }

            if ($item->hasError()) {
                ++$errors;
            } else {
                ++$success;
            }
        }

        $this->section('Results');
        $this->io->write([
            sprintf(' * Success: %d', $success),
            sprintf(' * Errors: %d', $errors),
            '',
        ]);
    }
}

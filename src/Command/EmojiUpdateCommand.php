<?php

declare(strict_types=1);

namespace Matecat\EmojiParser\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use stdClass;

/**
 * @codeCoverageIgnore
 */
class EmojiUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('emoji:update')
            ->setDescription('Update the emoji static map.')
            ->setHelp("Update the emoji static map with emoji-api.com API.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // SymfonyStyle
        $io = new SymfonyStyle($input, $output);
        $io->title('Update the emoji static map with emoji-api.com API');

        $apiKey = parse_ini_file(__DIR__ . '/../../config/credentials.ini');
        if ($apiKey === false || !isset($apiKey['emoji_api_key'])) {
            throw new RuntimeException('Cannot read credentials.ini or missing emoji_api_key.');
        }

        $url = 'https://emoji-api.com/emojis?access_key=' . $apiKey['emoji_api_key'];

        $response = file_get_contents($url);
        if ($response === false) {
            throw new RuntimeException('Failed to fetch emojis from API.');
        }

        /** @var list<stdClass>|null $emojis */
        $emojis = json_decode($response);
        if (!is_array($emojis)) {
            throw new RuntimeException('Failed to decode JSON response.');
        }

        $i = 1;
        $updates = 0;
        $skipped = 0;

        foreach ($emojis as $emoji) {
            $this->importEmoji($emoji, $io, $i, $updates, $skipped);

            if (isset($emoji->variants) && is_array($emoji->variants)) {
                /** @var stdClass $variant */
                foreach ($emoji->variants as $variant) {
                    $this->importEmoji($variant, $io, $i, $updates, $skipped);
                }
            }
        }

        $io->newLine();
        $io->writeln("========================================");
        $io->writeln("UPDATED: <fg=cyan>" . $updates . "</> SKIPPED: <fg=red>" . $skipped . "</>");
        $io->writeln("========================================");
        $io->newLine();

        return Command::SUCCESS;
    }

    private function importEmoji(stdClass $emoji, SymfonyStyle $io, int &$i, int &$updates, int &$skipped): void
    {
        /** @var string $character */
        $character = $emoji->character;
        /** @var string $slug */
        $slug = $emoji->slug;

        $htmlEntities = $this->convertEmojiToHtmlEntities($character);

        $chmapFile = __DIR__ . '/../chmap.php';

        /** @var array<string, string> $chmap */
        $chmap = include $chmapFile;
        $inverseChmap = array_flip($chmap);

        foreach ($htmlEntities as $char => $htmlEntity) {
            if (strlen($htmlEntity) >= 8) {
                if (!isset($inverseChmap[$htmlEntity])) {
                    $outcome = 'UPDATED';
                    $outcomeColor = 'cyan';
                    $updates++;
                    $chmap[$char] = $htmlEntity;
                } else {
                    $outcome = 'SKIPPED';
                    $outcomeColor = 'red';
                    $skipped++;
                }

                $io->writeln(
                    $i . '. Importing <fg=green>' . $slug . '</>...........<fg=' . $outcomeColor . '>' . $outcome . '</>'
                );
                $i++;

                file_put_contents($chmapFile, $this->generateTheChmapArray($chmap));
            }
        }
    }

    /**
     * @param array<string, string> $chmap
     */
    private function generateTheChmapArray(array $chmap): string
    {
        $chmapArray = "<?php ";
        $chmapArray .= PHP_EOL;
        $chmapArray .= PHP_EOL;
        $chmapArray .= "/**";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " * Note: for not visible characters:";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " *";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " * Launch IDE debug, and evaluate the expression:";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " *";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " * html_entity_decode('xxxx');";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " *";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " * and then copy the value";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " *";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " * @var array<string, string>";
        $chmapArray .= PHP_EOL;
        $chmapArray .= " */";
        $chmapArray .= PHP_EOL;
        $arrayValue = var_export($chmap, true);
        $arrayValue = str_replace('array (', '[', $arrayValue);
        $arrayValue = str_replace(')', ']', $arrayValue);
        $chmapArray .= "return " . $arrayValue . ";";
        $chmapArray .= PHP_EOL;

        return $chmapArray;
    }

    /**
     * @return array<string, string>
     */
    private function convertEmojiToHtmlEntities(string $emoji): array
    {
        $letters = preg_split('//u', $emoji, -1, PREG_SPLIT_NO_EMPTY);
        $entities = [];

        if ($letters === false) {
            return $entities;
        }

        foreach ($letters as $letter) {
            $utf32 = mb_convert_encoding($letter, 'UTF-32', 'UTF-8');
            $hex4 = bin2hex($utf32);
            $dec = hexdec($hex4);

            $entities[$letter] = '&#' . $dec . ';';
        }

        return $entities;
    }
}
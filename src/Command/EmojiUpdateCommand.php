<?php

namespace Matecat\EmojiParser\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EmojiUpdateCommand  extends Command
{
    protected function configure() {
        $this
            ->setName( 'emoji:update' )
            ->setDescription( 'Update the emoji static map.' )
            ->setHelp( "Update the emoji static map with emoji-api.com API." );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // SymfonyStyle
        $io = new SymfonyStyle( $input, $output );
        $io->title('Update the emoji static map with emoji-api.com API');

        $apiKey = parse_ini_file(__DIR__.'/../../config/credentials.ini');
        $url = 'https://emoji-api.com/emojis?access_key='.$apiKey['emoji_api_key'];

        $emojis = json_decode(file_get_contents($url));

        $i = 1;
        $updates = 0;
        $skipped = 0;

        foreach ($emojis as $emoji){
            $this->importEmoji($emoji, $i, $io, $updates, $skipped);
            $i++;

            if(isset($emoji->variants) and is_array($emoji->variants)){
                foreach ($emoji->variants as $variant){
                    $this->importEmoji($variant, $i, $io, $updates, $skipped);
                    $i++;
                }
            }
        }

        $io->newLine();
        $io->writeln("========================================");
        $io->writeln("UPDATED: <fg=cyan>".$updates."</> SKIPPED: <fg=red>".$skipped."</>");
        $io->writeln("========================================");
        $io->newLine();

        return Command::SUCCESS;
    }

    /**
     * @param $emoji
     * @param $i
     * @param SymfonyStyle $io
     * @param $updates
     * @param $skipped
     */
    private function importEmoji($emoji, $i, SymfonyStyle $io, &$updates, &$skipped)
    {
        if($i > 3600){
            $outcome = 'UPDATED';
            $outcomeColor = 'cyan';
            $updates++;
        } else {
            $outcome = 'SKIPPED';
            $outcomeColor = 'red';
            $skipped++;
        }

        $io->writeln(($i).'. Importing <fg=green>'.$emoji->slug.'</>...........<fg='.$outcomeColor.'>'.$outcome.'</>');
    }
}
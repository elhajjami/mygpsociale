<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agent;
use App\Models\Carriere;
use App\Models\Mesure;

class SynchroniserAgentsCarriere extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agents:sync-carriere';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchroniser les données de carrière et mesures vers les agents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Synchronisation des données de carrière et mesures...');

        $bar = $this->output->createProgressBar(Agent::count());
        $bar->start();

        $misAJour = 0;

        Agent::chunk(100, function ($agents) use (&$misAJour, $bar) {
            foreach ($agents as $agent) {
                // Récupérer la carrière actuelle
                $carriere = Carriere::where('matricule', $agent->matricule)
                    ->whereNull('date_fin')
                    ->orderBy('date_debut', 'desc')
                    ->first();

                if ($carriere) {
                    // Mettre à jour la catégorie
                    $agent->categorie = $carriere->categorie_complete;

                    // Mettre à jour le niveau si défini
                    if ($carriere->niv) {
                        $agent->niveau = $carriere->niv;
                    }
                    if ($carriere->deg) {
                        $agent->degre = $carriere->deg;
                    }
                }

                // Récupérer la dernière mesure
                $mesure = Mesure::where('matricule', $agent->matricule)
                    ->orderBy('date_debut', 'desc')
                    ->first();

                if ($mesure) {
                    // Mettre à jour le statut selon la mesure
                    if (stripos($mesure->motif ?? '', 'DE') !== false ||
                        stripos($mesure->motif ?? '', 'DECE') !== false ||
                        stripos($mesure->motif ?? '', 'DECES') !== false) {
                        $agent->statut = 'Sorti';
                    } elseif (stripos($mesure->motif ?? '', 'RETRA') !== false) {
                        $agent->statut = 'Retraité';
                    }
                }

                $agent->save();
                $misAJour++;
            }

            $bar->advance($agents->count());
        });

        $bar->finish();
        $this->newLine();
        $this->info("Terminé ! {$misAJour} agents mis à jour.");

        return Command::SUCCESS;
    }
}

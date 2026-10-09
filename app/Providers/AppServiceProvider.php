<?php

namespace App\Providers;

use App\Models\Contract;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            if (! auth()->check()) {
                $view->with('notificacoes', collect());
                return;
            }

            $hoje = now()->startOfDay();

            $notificacoes = Contract::with('group')
                // só a versão mais recente de cada contrato
                ->whereIn('id', function ($q) {
                    $q->selectRaw('MAX(id)')
                      ->from('contracts')
                      ->groupBy('contract_group_id');
                })
                ->where('prazo_indeterminado', false)
                ->whereNotNull('data_termino')
                ->whereDate('data_termino', '>=', $hoje->copy()->subDays(15))
                ->whereDate('data_termino', '<=', $hoje->copy()->addDays(30))
                ->orderBy('data_termino')
                ->limit(20)
                ->get()
                ->map(function ($contract) {
                    $dias = $contract->dias_para_vencer;

                    if ($dias < 0) {
                        $titulo = 'Contrato ' . $contract->tempo_restante;
                        $cor = 'text-red-600';
                    } elseif ($dias === 0) {
                        $titulo = 'Contrato vence hoje';
                        $cor = 'text-red-600';
                    } elseif ($dias === 1) {
                        $titulo = 'Contrato vence amanhã';
                        $cor = 'text-red-500';
                    } elseif ($dias <= 7) {
                        $titulo = "Contrato vence em {$dias} dias";
                        $cor = 'text-orange-500';
                    } else {
                        $titulo = "Contrato vence em {$dias} dias";
                        $cor = 'text-yellow-600';
                    }

                    return [
                        'id'      => $contract->id,
                        'titulo'  => $titulo,
                        'cor'     => $cor,
                        'cliente' => data_get($contract->contratante, 'nome') ?: 'Contratante não informado',
                        'imovel'  => collect([
                                         data_get($contract->imovel, 'tipo'),
                                         data_get($contract->imovel, 'localizacao'),
                                     ])->filter()->implode(' - ') ?: 'Imóvel não informado',
                        'data'    => $contract->data_termino->format('d/m/Y'),
                        'url'     => route('contracts.index'), // troque por contracts.show se existir
                    ];
                });

            $view->with('notificacoes', $notificacoes);
        });
    }
}

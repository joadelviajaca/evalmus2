<?php

namespace App\Filament\Resources\Evaluations\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.title')
                    ->label(__('Project'))
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label(__('Evaluator'))
                    ->searchable(),

                TextColumn::make('total_score')
                    ->label(__('Score'))
                    ->getStateUsing(fn ($record) => $record->totalScore())
                    ->badge()
                    ->color(fn ($state) => $state >= 5 ? 'success' : 'warning'),

                TextColumn::make('is_locked')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Cerrada' : 'Abierta')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('reopen')
                    ->label('Reabrir')
                    ->icon('heroicon-o-lock-open')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->is_locked)
                    ->action(function ($record) {
                        // Usamos una transacción para asegurar coherencia
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            // 1. Desbloquear la evaluación
                            $record->update(['is_locked' => false]);

                            // 2. Si el proyecto estaba finalizado, lo pasamos a evaluación
                            // porque acabamos de abrir una de sus partes.
                            if ($record->project->state === 'finished') {
                                $record->project->update(['state' => 'evaluating']);
                            }
                        });
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

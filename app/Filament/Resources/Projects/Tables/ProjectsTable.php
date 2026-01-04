<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('rubric.title')
                    ->label(__('Rubric'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
                    ->label(__('State'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'evaluating' => 'info',
                        'finished' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'evaluating' => 'En evaluación',
                        'finished' => 'Finalizado',
                        default => ucfirst($state),
                    }),
                TextColumn::make('evaluators.name')
                    ->label(__('Evaluators'))
                    ->badge()
                    ->separator(', ')
                    ->limitList(3)
                    ->tooltip(__('Click to view evaluators information'))
                    ->action(
                        Action::make('evaluatorsInfo')
                            ->label('')
                            ->modalHeading(__('Evaluators information'))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel(__('Close'))
                            ->modalCancelAction(fn ($action) =>
                                $action->color('primary')
                            )
                            ->schema(fn ($record) => [
                                Section::make(__('Evaluators'))
                                    ->description(__('List of evaluators assigned to this project.'))
                                    ->icon('heroicon-o-user-group')
                                    ->schema([
                                        RepeatableEntry::make('evaluators')
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label(__('Name'))
                                                    ->weight('bold')
                                                    ->icon('heroicon-o-user'),

                                                TextEntry::make('email')
                                                    ->label(__('Email'))
                                                    ->icon('heroicon-o-envelope'),
                                            ])
                                            ->columns(1)
                                            ->contained(true)
                                            ,
                                    ]),
                            ])
                    ),

                TextColumn::make('final_score')
                    ->label('Nota Media')
                    ->state(fn (Project $record) => $record->final_score) // Usamos el accessor
                    ->placeholder('Sin evaluar')
                    ->badge()
                    ->color(fn ($state) => $state >= 5 ? 'success' : 'danger')
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('download_report')
                    ->label('Informe PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Project $record) => route('projects.report', $record))
                    ->openUrlInNewTab(), // Opcional
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Pacientes\RelationManagers;

use App\Filament\Resources\Antecedentes\Tables\AntecedentesTable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Muestra los antecedentes directamente en la ficha del paciente (tab
 * dentro de Editar Paciente), mismo patrón que AlergiasRelationManager —
 * es un dato permanente del paciente, no de una consulta puntual
 * (MEMORIA.md sección 8).
 *
 * El formulario acá NO incluye paciente_id (a diferencia de
 * AntecedenteForm): el paciente ya está implícito por el registro dueño
 * (Paciente actual). `grupo_sanguineo` no vive acá — es un campo directo
 * del formulario de Paciente (ver PacienteForm), no una entrada de esta
 * lista categorizada.
 */
class AntecedentesRelationManager extends RelationManager
{
    protected static string $relationship = 'antecedentes';

    protected static ?string $title = 'Antecedentes';

    /**
     * Mismo criterio de acceso que AntecedenteResource: dato clínico
     * sensible, recepción no lo ve ni desde acá. Pendiente de confirmar
     * con el cliente (ver nota en AntecedenteResource).
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isMedico();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('categoria')
                    ->options([
                        'personal' => 'Personal',
                        'quirurgico' => 'Quirúrgico',
                        'familiar' => 'Familiar',
                        'habito' => 'Hábito',
                    ])
                    ->required(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->helperText('Ej. "Diabetes tipo 2", "Apendicectomía (2015)", "Madre con hipertensión", "Fuma 10 cigarrillos/día".')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                TextColumn::make('categoria')
                    ->badge()
                    ->color(fn (string $state): string => AntecedentesTable::colorCategoria($state))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50),
                TextColumn::make('notas')
                    ->limit(40)
                    ->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
            ]);
    }
}

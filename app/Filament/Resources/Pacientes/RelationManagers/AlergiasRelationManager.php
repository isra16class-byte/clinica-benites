<?php

namespace App\Filament\Resources\Pacientes\RelationManagers;

use App\Filament\Resources\Alergias\Tables\AlergiasTable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Muestra las alergias directamente en la ficha del paciente (tab dentro de
 * Editar Paciente) — es la razón de negocio de tener a Alergia en su propia
 * tabla en vez de texto libre (MEMORIA.md sección 8): "debe verse destacado
 * en la ficha del paciente", no escondido dentro de una nota.
 *
 * El formulario acá NO incluye paciente_id (a diferencia de AlergiaForm):
 * el paciente ya está implícito por el registro dueño (Paciente actual).
 *
 * Nota: sin ícono propio en el tab — no se pudo confirmar la propiedad
 * exacta contra el código fuente de Filament (sin `vendor/` disponible en
 * este sandbox), y se prefirió no arriesgar un tipo incorrecto. Es un
 * detalle cosmético, no bloquea la funcionalidad.
 */
class AlergiasRelationManager extends RelationManager
{
    protected static string $relationship = 'alergias';

    protected static ?string $title = 'Alergias';

    /**
     * Mismo criterio de acceso que AlergiaResource: dato clínico sensible,
     * recepción no lo ve ni desde acá. Pendiente de confirmar con el
     * cliente (ver nota en AlergiaResource).
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
                TextInput::make('alergeno')
                    ->label('Alérgeno')
                    ->helperText('Qué causa la alergia, ej. "Penicilina", "Maní".')
                    ->required(),
                Select::make('tipo')
                    ->options([
                        'medicamento' => 'Medicamento',
                        'alimento' => 'Alimento',
                        'otro' => 'Otro',
                    ])
                    ->required(),
                Select::make('severidad')
                    ->options([
                        'leve' => 'Leve',
                        'moderada' => 'Moderada',
                        'severa' => 'Severa',
                    ])
                    ->required(),
                Textarea::make('reaccion')
                    ->label('Reacción')
                    ->helperText('Qué reacción produce, ej. ronchas, hinchazón, anafilaxia.')
                    ->columnSpanFull(),
                Textarea::make('notas')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alergeno')
            ->columns([
                TextColumn::make('alergeno')
                    ->label('Alérgeno')
                    ->searchable(),
                TextColumn::make('tipo')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('severidad')
                    ->badge()
                    ->color(fn (string $state): string => AlergiasTable::colorSeveridad($state))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('reaccion')
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

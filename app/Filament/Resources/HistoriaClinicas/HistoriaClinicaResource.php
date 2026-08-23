<?php

namespace App\Filament\Resources\HistoriaClinicas;

use App\Filament\Resources\HistoriaClinicas\Pages\CreateHistoriaClinica;
use App\Filament\Resources\HistoriaClinicas\Pages\EditHistoriaClinica;
use App\Filament\Resources\HistoriaClinicas\Pages\ListHistoriaClinicas;
use App\Filament\Resources\HistoriaClinicas\Pages\ViewHistoriaClinica;
use App\Filament\Resources\HistoriaClinicas\Schemas\HistoriaClinicaForm;
use App\Filament\Resources\HistoriaClinicas\Schemas\HistoriaClinicaInfolist;
use App\Filament\Resources\HistoriaClinicas\Tables\HistoriaClinicasTable;
use App\Models\HistoriaClinica;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HistoriaClinicaResource extends Resource
{
    protected static ?string $model = HistoriaClinica::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return HistoriaClinicaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HistoriaClinicaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoriaClinicasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHistoriaClinicas::route('/'),
            'create' => CreateHistoriaClinica::route('/create'),
            'view' => ViewHistoriaClinica::route('/{record}'),
            'edit' => EditHistoriaClinica::route('/{record}/edit'),
        ];
    }
}

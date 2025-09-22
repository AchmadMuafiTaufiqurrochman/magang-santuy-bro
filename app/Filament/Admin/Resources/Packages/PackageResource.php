<?php

namespace App\Filament\Admin\Resources\Packages;

use App\Filament\Admin\Resources\Packages\Pages\CreatePackage;
use App\Filament\Admin\Resources\Packages\Pages\EditPackage;
use App\Filament\Admin\Resources\Packages\Pages\ListPackages;
use App\Filament\Admin\Resources\Packages\Schemas\PackageForm;
use App\Filament\Admin\Resources\Packages\Tables\PackagesTable;
use App\Models\Package;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Packages';
    protected static null|string $pluralLabel = 'Packages';
    protected static null|string $slug = 'packages';
    protected static null|int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'package';

    public static function form(Schema $schema): Schema
    {
        return PackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagesTable::configure($table);
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
            'index' => ListPackages::route('/'),
            'create' => CreatePackage::route('/create'),
            'edit' => EditPackage::route('/{record}/edit'),
        ];
    }
}

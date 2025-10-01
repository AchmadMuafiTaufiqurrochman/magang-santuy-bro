<?php

namespace App\Filament\Customer\Resources;

use App\Filament\Customer\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Package;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Schema;
use BackedEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'My Orders';

    protected static ?string $title = 'My Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
        // === SERVICE SELECTION ===
        Forms\Components\Select::make('package_id')
            ->label('Service Package (Optional)')
            ->placeholder('Select a service package...')
            ->options(Package::all()->pluck('name', 'id'))
            ->searchable()
            ->preload()
            ->live()
            ->disabled(fn ($record) => $record && !in_array($record->status, ['pending']))
            ->afterStateUpdated(function ($state, $set, $get) {
                $set('price_calculation', static::calculateTotalPrice($get));
            })
            ->helperText('Choose a service package or skip to select individual products'),

        Forms\Components\Select::make('selected_products')
            ->label('Individual Products (Optional)')
            ->placeholder('Select individual products...')
            ->options(function() {
                return Product::all()->map(function($product) {
                    return $product->name . ' - Rp ' . number_format($product->price, 0, ',', '.');
                })->toArray();
            })
            ->multiple()
            ->searchable()
            ->preload()
            ->live()
            ->disabled(fn ($record) => $record && !in_array($record->status, ['pending']))
            ->afterStateUpdated(function ($state, $set, $get) {
                $set('price_calculation', static::calculateTotalPrice($get));
            })
            ->afterStateHydrated(function ($component, $get, $set, $state) {
                // Update price calculation saat form di-load dengan data existing
                $set('price_calculation', static::calculateTotalPrice($get));
            })
            ->helperText('Select multiple products - hold Ctrl/Cmd to select multiple items'),

        // === SCHEDULE & LOCATION ===
        Forms\Components\DatePicker::make('date')
            ->label('Service Date')
            ->required()
            ->minDate(now()->addDay())
            ->maxDate(now()->addMonths(3))
            ->disabled(fn ($record) => $record && !in_array($record->status, ['pending']))
            ->helperText('Select your preferred service date'),

        Forms\Components\TimePicker::make('time_slot')
            ->label('Preferred Time')
            ->required()
            ->seconds(false)
            ->minutesStep(30)
            ->default('09:00')
            ->disabled(fn ($record) => $record && !in_array($record->status, ['pending']))
            ->helperText('Choose your preferred time slot'),

        Forms\Components\Textarea::make('address')
            ->label('Service Address')
            ->required()
            ->rows(3)
            ->maxLength(500)
            ->placeholder('Please provide your complete address for service delivery...')
            ->disabled(fn ($record) => $record && !in_array($record->status, ['pending'])),

        Forms\Components\Textarea::make('note')
            ->label('Additional Notes')
            ->rows(2)
            ->maxLength(255)
            ->placeholder('Any special instructions or requests...')
            ->disabled(fn ($record) => $record && !in_array($record->status, ['pending'])),

        // === ORDER SUMMARY ===
        Forms\Components\TextInput::make('price_calculation')
            ->label('💰 Total Price')
            ->disabled()
            ->default('Rp 0')
            ->dehydrated(false)
            ->prefix('Total:')
            ->afterStateHydrated(function ($component, $get, $state) {
                // Hitung total price saat form di-load untuk edit
                $component->state(static::calculateTotalPrice($get));
            }),

        // --- Hidden fields (supaya konsisten dengan daftar order) ---
        Forms\Components\Hidden::make('status')
            ->default('pending'),

        Forms\Components\Hidden::make('transaction_status')
            ->default('pending'),
        ]);
    }

    protected static function calculateTotalPrice($get): string
    {
        $totalPrice = 0;

        // Hitung harga package jika dipilih
        $packageId = $get('package_id');
        if ($packageId) {
            $package = Package::find($packageId);
            if ($package) {
                $totalPrice += $package->price;
            }
        }

        // Hitung harga products jika dipilih
        $selectedProducts = $get('selected_products');
        if ($selectedProducts && is_array($selectedProducts) && count($selectedProducts) > 0) {
            $products = Product::whereIn('id', $selectedProducts)->get();
            foreach ($products as $product) {
                $totalPrice += $product->price;
            }
        }

        if ($totalPrice > 0) {
            return "Rp " . number_format($totalPrice, 0, ',', '.');
        }

        return 'Rp 0';
    }

    public static function table(Table $table): Table
    {
        // columns
        $columns = [
            TextColumn::make('id')
                ->label('Order ID')
                ->sortable()
                ->searchable()
                ->weight(FontWeight::Bold),

            TextColumn::make('services')
                ->label('Services Ordered')
                ->searchable()
                ->sortable()
                ->wrap()
                ->getStateUsing(function ($record) {
                    $services = [];

                    // Tampilkan package jika ada
                    if ($record->package) {
                        $services[] = 'Package: ' . $record->package->name;
                    }

                    // Tampilkan selected products jika ada
                    $selectedProducts = $record->selectedProducts();
                    if ($selectedProducts->count() > 0) {
                        $productNames = $selectedProducts->pluck('name')->toArray();
                        $services[] = 'Products: ' . implode(', ', $productNames);
                    }

                    return !empty($services) ? implode(' | ', $services) : 'No Services';
                }),

            TextColumn::make('date')
                ->date('d M Y')
                ->sortable(),

            TextColumn::make('time_slot')
                ->time('H:i')
                ->sortable(),

            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'assigned' => 'info',
                    'in_progress' => 'primary',
                    'done' => 'success',
                    'cancelled' => 'danger',
                })
                ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),

            TextColumn::make('total_price')
                ->label('Total Price')
                ->money('IDR')
                ->sortable()
                ->getStateUsing(function ($record) {
                    $total = 0;

                    // Harga package jika ada
                    if ($record->package) {
                        $total += $record->package->price;
                    }

                    // Harga selected products jika ada
                    $selectedProducts = $record->selectedProducts();
                    foreach ($selectedProducts as $product) {
                        $total += $product->price;
                    }

                    return $total;
                }),

            TextColumn::make('transaction.status')
                ->label('Payment')
                ->badge()
                ->color(fn ($state): string => match ($state) {
                    'pending' => 'warning',
                    'paid' => 'success',
                    'failed' => 'danger',
                    'cancelled' => 'gray',
                    default => 'gray',
                })
                ->formatStateUsing(fn ($state): string => $state ? ucfirst($state) : 'No Payment'),

            TextColumn::make('created_at')
                ->label('Ordered At')
                ->dateTime('d M Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];

        // filters
        $filters = [
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'assigned' => 'Assigned',
                    'in_progress' => 'In Progress',
                    'done' => 'Done',
                    'cancelled' => 'Cancelled',
                ]),

            Tables\Filters\Filter::make('date_range')
                ->form([
                    Forms\Components\DatePicker::make('from_date')->label('From Date'),
                    Forms\Components\DatePicker::make('to_date')->label('To Date'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from_date'] ?? null, fn (Builder $q, $date) => $q->whereDate('date', '>=', $date))
                        ->when($data['to_date'] ?? null, fn (Builder $q, $date) => $q->whereDate('date', '<=', $date));
                }),
        ];

        // build actions robust terhadap versi Filament
        $actions = [];

        // 1) View action: cek beberapa namespace
        if (class_exists(\Filament\Tables\Actions\ViewAction::class)) {
            $actions[] = \Filament\Tables\Actions\ViewAction::make();
        } elseif (class_exists(\Filament\Actions\ViewAction::class)) {
            $actions[] = \Filament\Actions\ViewAction::make();
        } elseif (class_exists(\Filament\Tables\Actions\Action::class)) {
            $actions[] = \Filament\Tables\Actions\Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->url(fn (Order $record): string => static::getUrl('view', ['record' => $record]));
        } elseif (class_exists(\Filament\Actions\Action::class)) {
            $actions[] = \Filament\Actions\Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->url(fn (Order $record): string => static::getUrl('view', ['record' => $record]));
        }

        // 2) Edit action
        if (class_exists(\Filament\Tables\Actions\EditAction::class)) {
            $actions[] = \Filament\Tables\Actions\EditAction::make()
                ->visible(fn (Order $record): bool => in_array($record->status, ['pending']));
        } elseif (class_exists(\Filament\Actions\EditAction::class)) {
            $actions[] = \Filament\Actions\EditAction::make()
                ->visible(fn (Order $record): bool => in_array($record->status, ['pending']));
        } elseif (class_exists(\Filament\Tables\Actions\Action::class)) {
            $actions[] = \Filament\Tables\Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(fn (Order $record): string => static::getUrl('edit', ['record' => $record]))
                ->visible(fn (Order $record): bool => in_array($record->status, ['pending']));
        } elseif (class_exists(\Filament\Actions\Action::class)) {
            $actions[] = \Filament\Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(fn (Order $record): string => static::getUrl('edit', ['record' => $record]))
                ->visible(fn (Order $record): bool => in_array($record->status, ['pending']));
        }

        // 3) Custom cancel action (jika available)
        if (class_exists(\Filament\Tables\Actions\Action::class)) {
            $actions[] = \Filament\Tables\Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'assigned']))
                ->action(fn (Order $record) => $record->update(['status' => 'cancelled']));
        } elseif (class_exists(\Filament\Actions\Action::class)) {
            $actions[] = \Filament\Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Order $record): bool => in_array($record->status, ['pending', 'assigned']))
                ->action(fn (Order $record) => $record->update(['status' => 'cancelled']));
        }

        // build bulkActions defensif
        $bulkActions = [];
        if (class_exists(\Filament\Tables\Actions\BulkActionGroup::class) && class_exists(\Filament\Tables\Actions\DeleteBulkAction::class)) {
            $bulkActions[] = \Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make()->visible(fn (): bool => false),
            ]);
        } elseif (class_exists(\Filament\Tables\Actions\DeleteBulkAction::class)) {
            $bulkActions[] = \Filament\Tables\Actions\DeleteBulkAction::make()->visible(fn (): bool => false);
        }

        // return built table
        return $table
            ->columns($columns)
            ->filters($filters)
            ->actions($actions)
            ->bulkActions($bulkActions)
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto refresh
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->with(['package', 'transaction']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        return $count > 0 ? (string) $count : null;
    }
}

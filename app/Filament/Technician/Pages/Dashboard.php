<?php

namespace App\Filament\Technician\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Livewire\Attributes\Computed;
use App\Models\OrderAssignment;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Dashboard extends Page implements HasTable
{
    use WithFileUploads, InteractsWithTable;
    
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.technician.pages.dashboard';

    protected static ?string $title = 'Dashboard Technician';

    #[Computed]
    public function isAvailable(): bool
    {
        return auth()->user()->technician_status === 'available';
    }

    public function toggleAvailability(): void
    {
        $user = auth()->user();
        $currentStatus = $user->technician_status ?? 'offline';

        $newStatus = $currentStatus === 'available' ? 'offline' : 'available';

        $user->update(['technician_status' => $newStatus]);

        // Refresh the component
        $this->dispatch('$refresh');
    }

    #[Computed]
    public function todayOrders()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereDate('assigned_at', today())
            ->with('order.user', 'order.package')
            ->get();
    }

    #[Computed] 
    public function completedToday()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereHas('order', function($query) {
                $query->where('status', 'done');
            })
            ->whereDate('assigned_at', today())
            ->count();
    }

    #[Computed]
    public function weekCount()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereBetween('assigned_at', [$weekStart, $weekEnd])
            ->count();
    }

    #[Computed]
    public function monthCount()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereMonth('assigned_at', now()->month)
            ->count();
    }

    #[Computed]
    public function recentActivity()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->with('order.package', 'order.user')
            ->orderBy('assigned_at', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function pendingOrders()
    {
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            })
            ->with('order.user', 'order.package')
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    #[Computed] 
    public function newAssignments()
    {
        // Orders yang baru di-assign dalam 24 jam terakhir dan belum di-accept
        return \App\Models\OrderAssignment::where('technician_id', auth()->id())
            ->whereHas('order', function($query) {
                $query->where('status', 'assigned');
            })
            ->where('assigned_at', '>=', now()->subDay())
            ->with('order.user', 'order.package')
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    public function acceptOrder($assignmentId)
    {
        $assignment = \App\Models\OrderAssignment::where('id', $assignmentId)
            ->where('technician_id', auth()->id())
            ->first();

        if ($assignment) {
            $assignment->order->update(['status' => 'in_progress']);
            
            \Filament\Notifications\Notification::make()
                ->title('Order Accepted!')
                ->body("Order #{$assignment->order->id} has been accepted and is now in progress.")
                ->success()
                ->send();

            // Refresh the component
            $this->dispatch('$refresh');
        }
    }

    public function rejectOrder($assignmentId)
    {
        $assignment = \App\Models\OrderAssignment::where('id', $assignmentId)
            ->where('technician_id', auth()->id())
            ->first();

        if ($assignment) {
            // Delete the assignment and reset order status to pending
            $assignment->order->update(['status' => 'pending']);
            $assignment->delete();
            
            \Filament\Notifications\Notification::make()
                ->title('Order Rejected')
                ->body("Order #{$assignment->order->id} has been rejected and returned to pending status.")
                ->warning()
                ->send();

            // Refresh the component
            $this->dispatch('$refresh');
        }
    }

    public $showCameraModal = false;
    public $selectedOrderId = null;
    public $capturedPhotoData = null;
    public $completionNotes = '';

    public function openCameraModal($assignmentId)
    {
        // Check if technician is available/online
        if (!$this->isAvailable) {
            \Filament\Notifications\Notification::make()
                ->title('Status Offline')
                ->body('Anda harus dalam status online untuk mengambil foto bukti penyelesaian.')
                ->warning()
                ->send();
            return;
        }

        $assignment = \App\Models\OrderAssignment::where('id', $assignmentId)
            ->where('technician_id', auth()->id())
            ->first();

        if ($assignment && $assignment->order->status === 'in_progress') {
            $this->selectedOrderId = $assignment->order->id;
            $this->showCameraModal = true;
            $this->completionNotes = '';
            $this->capturedPhotoData = null;
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Order Not Found')
                ->body('Order tidak ditemukan atau status tidak sesuai.')
                ->warning()
                ->send();
        }
    }

    public function closeCameraModal()
    {
        $this->showCameraModal = false;
        $this->selectedOrderId = null;
        $this->capturedPhotoData = null;
        $this->completionNotes = '';
    }

    public function capturePhoto($photoData)
    {
        // Double check if technician is still online
        if (!$this->isAvailable) {
            \Filament\Notifications\Notification::make()
                ->title('Status Berubah')
                ->body('Status Anda telah berubah menjadi offline. Silakan online kembali untuk melanjutkan.')
                ->warning()
                ->send();
            $this->closeCameraModal();
            return;
        }

        $this->capturedPhotoData = $photoData;
    }

    public function retakePhoto()
    {
        $this->capturedPhotoData = null;
    }



    public function completeOrder()
    {
        // Validate that photo was captured from camera
        if (!$this->capturedPhotoData) {
            \Filament\Notifications\Notification::make()
                ->title('Foto Diperlukan')
                ->body('Anda harus mengambil foto menggunakan kamera untuk menyelesaikan order.')
                ->warning()
                ->send();
            return;
        }

        $this->validate([
            'completionNotes' => 'nullable|string|max:500'
        ]);

        if ($this->selectedOrderId && $this->capturedPhotoData) {
            $order = \App\Models\Order::find($this->selectedOrderId);
            
            if ($order && $order->status === 'in_progress') {
                // Convert base64 to image file
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->capturedPhotoData));
                $filename = 'completion_' . $order->id . '_' . time() . '.jpg';
                $path = 'completion-photos/' . $filename;
                
                // Ensure directory exists
                $fullPath = storage_path('app/public/' . dirname($path));
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0755, true);
                }
                
                // Store in public disk
                $stored = Storage::disk('public')->put($path, $imageData);
                
                // Log for debugging
                \Log::info('Photo storage attempt', [
                    'order_id' => $order->id,
                    'filename' => $filename,
                    'path' => $path,
                    'full_storage_path' => storage_path('app/public/' . $path),
                    'stored' => $stored,
                    'file_exists_after_storage' => file_exists(storage_path('app/public/' . $path)),
                    'image_data_size' => strlen($imageData)
                ]);
                
                // Update order
                $order->update([
                    'status' => 'done',
                    'completion_photo' => $path,
                    'completion_notes' => $this->completionNotes,
                    'completed_at' => now()
                ]);

                \Filament\Notifications\Notification::make()
                    ->title('Order Completed!')
                    ->body("Order #{$order->id} has been marked as completed with photo evidence.")
                    ->success()
                    ->send();

                $this->closeCameraModal();
                $this->dispatch('$refresh');
            }
        }
    }

    // Remove all widgets
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getWidgets(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderAssignment::where('technician_id', auth()->id())
                    ->with(['order.user', 'order.package'])
                    ->orderBy('assigned_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('order.user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.package.name')
                    ->label('Service')
                    ->default('Custom Service')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.date')
                    ->label('Service Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.time_slot')
                    ->label('Time')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('order.address')
                    ->label('Address')
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('order.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'assigned' => 'info',
                        'in_progress' => 'primary',
                        'done' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '⏳ Pending',
                        'assigned' => '📝 Assigned',
                        'in_progress' => '🔄 In Progress',
                        'done' => '✅ Completed',
                        'cancelled' => '❌ Cancelled',
                        default => ucfirst($state)
                    }),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Assigned At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Order Status')
                    ->options([
                        'assigned' => 'Assigned',
                        'in_progress' => 'In Progress',
                        'done' => 'Completed',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('order', function ($q) use ($data) {
                                $q->where('status', $data['value']);
                            });
                        }
                    }),
                
                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Orders')
                    ->query(fn (Builder $query) => $query->whereDate('assigned_at', today())),
            ])
            ->actions([
                Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->order->status === 'assigned')
                    ->action(function ($record) {
                        $this->acceptOrder($record->id);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->order->status === 'assigned')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $this->rejectOrder($record->id);
                    }),

                Action::make('complete')
                    ->label('Complete with Camera')
                    ->icon('heroicon-o-camera')
                    ->color('success')
                    ->visible(fn ($record) => $record->order->status === 'in_progress')
                    ->action(function ($record) {
                        $this->openCameraModal($record->id);
                    }),

                Action::make('view_details')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalContent(function ($record) {
                        return view('filament.technician.pages.order-details-modal', [
                            'assignment' => $record
                        ]);
                    })
                    ->modalHeading(fn ($record) => 'Order #' . $record->order->id . ' Details')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->headerActions([
                Action::make('toggle_availability')
                    ->label(fn () => $this->isAvailable ? 'Go Offline' : 'Go Online')
                    ->icon(fn () => $this->isAvailable ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn () => $this->isAvailable ? 'danger' : 'success')
                    ->action(function () {
                        $this->toggleAvailability();
                    }),
            ])
            ->emptyStateHeading('No Orders Yet')
            ->emptyStateDescription('You haven\'t been assigned any orders yet.')
            ->emptyStateIcon('heroicon-o-inbox')
            ->poll('30s');
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalistResource\Pages;
use App\Filament\Resources\JournalistResource\RelationManagers;
use App\Models\Journalist;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Font;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction as TablesExportBulkAction;
use Illuminate\Contracts\Support\Htmlable;


class JournalistResource extends Resource
{

    // protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?string $model = Journalist::class;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'contact', 'email','category.name'];
    }


    protected static int $globalSearchResultsLimit = 10;

    protected static ?string $navigationLabel = 'Medios de Prensa';
    protected static ?string $pluralLabel = 'Medios de Prensa';
    protected static ?string $modelLabel = 'Medios de Prensa';



    protected static ?string $navigationIcon = 'heroicon-o-user-group';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('folder')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('file')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('sheet'),
                Forms\Components\TextInput::make('ccaa'),
                Forms\Components\TextInput::make('name')
                    ->placeholder('Nombre del medio de prensa')
                    ->label('Medio')
                    ->required(),
                Forms\Components\TextInput::make('contact')
                    ->label('Contacto'),
                Forms\Components\Select::make('position')
                    ->label('Cargo')
                    ->relationship('position', 'position')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('position')
                            ->label('Cargo')
                            ->required(),
                        Forms\Components\TextArea::make('description')
                            ->label('Descripción')
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email(),
                Forms\Components\Select::make('geographical_scope')
                    ->options([
                        'Local' => 'Local',
                        'Provincial' => 'Provincial',
                        'Autonómico' => 'Autonómico',
                        'Nacional' => 'Nacional',
                        'Internacional' => 'Internacional',
                    ])
                    ->label('Ámbito Geográfico'),
                Forms\Components\Select::make('category')
                        ->placeholder('Selecciona una categoría')
                        ->relationship('category', 'name')
                        ->label('Categoría')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Categoría')
                                ->required(),
                            Forms\Components\TextArea::make('description')
                                ->label('Descripción')
                        ]),
                Forms\Components\Select::make('type')
                        ->placeholder('Seleccione un tipo')
                        ->relationship('type', 'name')
                        ->label('Tipo')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Tipo')
                                ->required(),
                            Forms\Components\TextArea::make('description')
                                ->label('Descripción')
                        ]),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->maxLength(255)
                    ->autosize()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'Activo',
                        1 => 'Baja'
                    })
                    ->color(fn($record) => match ($record->status) {
                        0 => 'success',
                        1 => 'danger',
                    }),
                Tables\Columns\TextColumn::make('folder')
                    ->label('Carpeta')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('file')
                    ->label('Archivo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sheet')
                    ->label('Hoja')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ccaa')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('geographical_scope')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Ámbito Geográfico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Categoría')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->weight(FontWeight::Bold)
                    ->limit(20)
                    ->color('primary')
                    ->label('Medio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position.position')
                    ->label('Cargo')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->copyMessage('Email copiado')
                    ->icon('heroicon-o-clipboard-document')
                    ->iconColor('gray')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchPlaceholder('Buscar medios...')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        0 => 'Activos',
                        1 => 'Baja',
                    ]),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->relationship('type', 'name'),
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->relationship('category', 'name'),
                SelectFilter::make('position')
                    ->label('Cargo')
                    ->searchable()
                    ->preload()
                    ->relationship('position', 'position'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('primary')
                        ->label('Editar'),
                    Tables\Actions\ViewAction::make()
                        ->label('Ver'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Eliminar'),
                    Tables\Actions\ReplicateAction::make()
                        ->beforeReplicaSaved(function ($replica) {

                            $replica->name = $replica->name . ' - DUPLICADO';
                            $replica->user_id = Auth::id(); // Asigna el usuario actual
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Medio duplicado')
                                ->body('El medio ha sido duplicado correctamente')
                        )
                        ->label('Duplicar')
                        ->color('info'),
                    Tables\Actions\Action::make('toglleStatus')
                        ->label('Cambiar estado')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(fn($record) => $record->update(['status' => $record->status ? 0 : 1]))
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                    TablesExportBulkAction::make()
                        ->label('Exportar seleccionados')

                ]),
            ]);
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
            'index' => Pages\ListJournalists::route('/'),
            'create' => Pages\CreateJournalist::route('/create'),
            'edit' => Pages\EditJournalist::route('/{record}/edit'),
        ];
    }
}

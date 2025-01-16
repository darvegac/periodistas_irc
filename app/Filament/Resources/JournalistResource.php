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

class JournalistResource extends Resource
{

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $model = Journalist::class;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'contact', 'email'];
    }

    protected static int $globalSearchResultsLimit = 10;

    protected static ?string $navigationLabel = 'Medios de Prensa';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('folder')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('file')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('sheet')
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('ccaa')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->placeholder('Nombre del medio de prensa')
                    ->label('Medio')
                    ->required(),
                Forms\Components\TextInput::make('contact')
                    ->label('Contacto'),
                Forms\Components\TextInput::make('position')
                    ->label('Cargo'),
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
                    ->searchable()
                    ->options([
                        'Tercera edad' => 'Tercera edad',
                        'Energía' => 'Energía',
                        'Construcción' => 'Construcción',
                        'Construcción y Turismo' => 'Construcción y Turismo',
                        'Turismo' => 'Turismo',
                        'RSC' => 'RSC',
                        'Generalistas' => 'Generalistas',
                        'Formación' => 'Formación',
                        'Mujeres' => 'Mujeres',
                        'Discapacidad' => 'Discapacidad',
                        'Mascotas' => 'Mascotas',
                        'Economía' => 'Economía',
                        'Comunicación' => 'Comunicación',
                        'Salud' => 'Salud',
                        'Revistas marca personal' => 'Revistas marca personal',
                        'Revistas viajes y ocio' => 'Revistas viajes y ocio',
                        'Revistas Moda y  Lifestyle' => 'Revistas Moda y  Lifestyle',
                        'Revistas Masculinas' => 'Revistas Masculinas',
                        'Grupo Prensa Ibérica' => 'Grupo Prensa Ibérica',
                        'Grupo Publicación Heres' => 'Grupo Publicación Heres',
                        'Grupo Hola' => 'Grupo Hola',
                        'Grupo ZinetMedia Group' => 'Grupo ZinetMedia Group',
                        'Grupo Condenast' => 'Grupo Condenast',
                        'Grupo Mediaset y G+ J' => 'Grupo Mediaset y G+ J',
                        'Grupo Zeta' => 'Grupo Zeta',
                        'Grupo RBA' => 'Grupo RBA',
                        'Grupo Hearst' => 'Grupo Hearst',
                        'Agencias' => 'Agencias',
                        'Revistas Femeninas' => 'Revistas Femeninas',
                        'Grupo Unidad Editorial' => 'Grupo Unidad Editorial',
                        'Grupo Godó' => 'Grupo Godó',
                        'Grupo Prisa' => 'Grupo Prisa',
                        'Suplementos Medios Generalistas' => 'Suplementos Medios Generalistas',
                        'Ciencia' => 'Ciencia',
                        'Música' => 'Música',
                        'Teatro' => 'Teatro',
                        'Maternidad' => 'Maternidad',
                        'Cultura' => 'Cultura',
                        'Poesía, Arte y Pensamiento' => 'Poesía, Arte y Pensamiento',
                        'Literatura' => 'Literatura',
                        'Pensamiento y Cultura' => 'Pensamiento y Cultura',
                        'Política y Cultura' => 'Política y Cultura',
                        'Biografías' => 'Biografías',
                        'CCSS' => 'CCSS',
                        'Arquitectura' => 'Arquitectura',
                        'Medioambiente / Política' => 'Medioambiente / Política',
                        'Política' => 'Política',
                        'Cine' => 'Cine',
                        'Arte' => 'Arte',
                        'Educación' => 'Educación',
                        'Historia' => 'Historia',
                        'Cultura y CCSS' => 'Cultura y CCSS',
                        'Feminismo' => 'Feminismo',
                        'Revista' => 'Revista',
                        'Periódico' => 'Periódico',
                        'Televisión' => 'Televisión',
                        'Radio' => 'Radio',
                        'Web' => 'Web',
                        'Freelance' => 'Freelance',
                        'Noticias' => 'Noticias',
                        'Programa' => 'Programa',
                    ])
                    ->label('Categoría'),
                Forms\Components\Select::make('type')
                        ->placeholder('Seleccione un tipo')
                    ->searchable()
                    ->options([
                        'Generalista' => 'Generalista',
                        'Televisión' => 'Televisión',
                        'Especializado' => 'Especializado',
                        'Agencia' => 'Agencia',
                        'Radio' => 'Radio',
                        'Económico' => 'Económico',
                        'Prensa' => 'Prensa',
                        'Digital' => 'Digital',
                        'Freelance' => 'Freelance',
                        'Asociación' => 'Asociación',
                        'Suplemento' => 'Suplemento',
                    ])
                    ->label('Tipo'),
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
                Tables\Columns\TextColumn::make('category')
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
                Tables\Columns\TextColumn::make('position')
                    ->label('Cargo')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
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
                    ->options([
                        'Generalista' => 'Generalista',
                        'Televisión' => 'Televisión',
                        'Especializado' => 'Especializado',
                        'Agencia' => 'Agencia',
                        'Radio' => 'Radio',
                        'Económico' => 'Económico',
                        'Prensa' => 'Prensa',
                        'Digital' => 'Digital',
                        'Freelance' => 'Freelance',
                        'Asociación' => 'Asociación',
                        'Suplemento' => 'Suplemento',
                    ]),
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileAttachmentResource\Pages;
use App\Filament\Resources\FileAttachmentResource\RelationManagers;
use App\Models\FileAttachment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class FileAttachmentResource extends Resource
{
    protected static ?string $model = FileAttachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('file_path')
                    ->label('File')
                    ->preserveFilenames()
                    ->disk('private')
                    ->directory(function () {
                        $rand = Carbon::now()->timestamp . '-' . Str::random(2);
                        return "attachments/" . $rand;
                    })
                    ->visibility('private')
                    ->openable()
                    ->deletable(true)
                    ->reorderable()
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->html()
                    ->limit(100)
                    ->wrap(),
                Tables\Columns\TextColumn::make('file_path')
//                    ->formatUsing(function ($value) {
//                        return basename($value);
//                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_size')
//                    ->formatUsing(function ($value) {
//                        return Str::of($value)->replace('bytes', 'B')->replace('KB', 'KB')->replace('MB', 'MB')->replace('GB', 'GB')->replace('TB', 'TB');
//                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploaded_by')
//                    ->formatUsing(function ($value) {
//                        return $value ? $value : 'System';
//                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
//                    ->formatUsing(function ($value) {
//                        return Carbon::parse($value)->format('Y-m-d H:i:s');
//                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListFileAttachments::route('/'),
            'create' => Pages\CreateFileAttachment::route('/create'),
            'edit' => Pages\EditFileAttachment::route('/{record}/edit'),
        ];
    }
}

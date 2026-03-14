<?php
namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?string $modelLabel = '文件';
    protected static ?string $pluralModelLabel = '文件管理';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('文件資訊')->schema([
                Forms\Components\TextInput::make('title')->label('標題（中文）')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('title_en')->label('標題（英文）')->maxLength(255)->columnSpanFull(),
                Forms\Components\Textarea::make('description')->label('說明')->rows(3)->columnSpanFull(),
                Forms\Components\Select::make('category')->label('類別')
                    ->options(['minutes' => '會議記錄', 'newsletter' => '社訊', 'report' => '報告', 'policy' => '政策文件', 'other' => '其他'])
                    ->required(),
                Forms\Components\Select::make('visibility')->label('可見範圍')
                    ->options(['public' => '公開', 'members' => '所有會員', 'club_admin' => '社團管理員', 'hq_admin' => '總部管理員'])
                    ->required()->default('members'),
                Forms\Components\Select::make('club_id')->label('所屬社團')->relationship('club', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('district_id')->label('所屬地區')->relationship('district', 'name')->searchable()->preload()->nullable(),
            ])->columns(2),
            Forms\Components\Section::make('檔案上傳')->schema([
                Forms\Components\FileUpload::make('file_path')->label('上傳檔案')
                    ->directory('documents')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(10240)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('標題')->sortable()->searchable()->limit(50),
                Tables\Columns\BadgeColumn::make('category')->label('類別')
                    ->formatStateUsing(fn($state) => match($state) {
                        'minutes' => '會議記錄', 'newsletter' => '社訊', 'report' => '報告', 'policy' => '政策', 'other' => '其他', default => $state
                    }),
                Tables\Columns\BadgeColumn::make('visibility')->label('可見範圍')
                    ->formatStateUsing(fn($state) => match($state) {
                        'public' => '公開', 'members' => '會員', 'club_admin' => '社團管理', 'hq_admin' => '總部管理', default => $state
                    }),
                Tables\Columns\TextColumn::make('club.name')->label('所屬社團')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('download_count')->label('下載次數')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('上傳時間')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->label('類別')
                    ->options(['minutes' => '會議記錄', 'newsletter' => '社訊', 'report' => '報告', 'policy' => '政策', 'other' => '其他']),
                Tables\Filters\SelectFilter::make('visibility')->label('可見範圍')
                    ->options(['public' => '公開', 'members' => '會員', 'club_admin' => '社團管理', 'hq_admin' => '總部管理']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

<?php
namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = '內容管理';
    protected static ?string $modelLabel = '公告';
    protected static ?string $pluralModelLabel = '公告管理';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('公告內容')->schema([
                Forms\Components\TextInput::make('title')->label('標題（中文）')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('title_en')->label('標題（英文）')->maxLength(255)->columnSpanFull(),
                Forms\Components\RichEditor::make('content')->label('內容（中文）')->required()->columnSpanFull(),
                Forms\Components\RichEditor::make('content_en')->label('內容（英文）')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('發布設定')->schema([
                Forms\Components\Select::make('target')->label('發布對象')
                    ->options(['all' => '所有會員', 'district' => '地區會員', 'club' => '社團會員', 'public' => '公開'])
                    ->default('all')->required(),
                Forms\Components\Select::make('club_id')->label('社團')->relationship('club', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('district_id')->label('地區')->relationship('district', 'name')->searchable()->preload()->nullable(),
                Forms\Components\DateTimePicker::make('publish_date')->label('發布日期'),
                Forms\Components\DateTimePicker::make('expiration_date')->label('到期日期'),
                Forms\Components\Toggle::make('is_pinned')->label('置頂'),
                Forms\Components\Select::make('status')->label('狀態')
                    ->options(['draft' => '草稿', 'published' => '已發布', 'archived' => '已封存'])
                    ->default('draft')->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_pinned')->label('置頂')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('title')->label('標題')->sortable()->searchable()->limit(50),
                Tables\Columns\BadgeColumn::make('target')->label('對象')
                    ->formatStateUsing(fn($state) => match($state) {
                        'all' => '所有', 'district' => '地區', 'club' => '社團', 'public' => '公開', default => $state
                    }),
                Tables\Columns\BadgeColumn::make('status')->label('狀態')
                    ->colors(['secondary' => 'draft', 'success' => 'published', 'warning' => 'archived'])
                    ->formatStateUsing(fn($state) => match($state) {
                        'draft' => '草稿', 'published' => '已發布', 'archived' => '已封存', default => $state
                    }),
                Tables\Columns\TextColumn::make('publish_date')->label('發布日期')->dateTime()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('expiration_date')->label('到期日期')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('狀態')
                    ->options(['draft' => '草稿', 'published' => '已發布', 'archived' => '已封存']),
                Tables\Filters\SelectFilter::make('target')->label('對象')
                    ->options(['all' => '所有', 'district' => '地區', 'club' => '社團', 'public' => '公開']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('is_pinned', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

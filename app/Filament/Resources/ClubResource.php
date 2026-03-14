<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ClubResource\Pages;
use App\Models\Club;
use App\Models\District;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClubResource extends Resource
{
    protected static ?string $model = Club::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = '組織管理';
    protected static ?string $modelLabel = '社團';
    protected static ?string $pluralModelLabel = '社團管理';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('基本資訊')->schema([
                Forms\Components\TextInput::make('name')->label('社團名稱（中文）')->required()->maxLength(255),
                Forms\Components\TextInput::make('name_en')->label('社團名稱（英文）')->maxLength(255),
                Forms\Components\TextInput::make('club_code')->label('社團代碼')->required()->unique(ignoreRecord: true)->maxLength(50),
                Forms\Components\Select::make('district_id')->label('所屬地區')->relationship('district', 'name')->required()->searchable()->preload(),
                Forms\Components\Toggle::make('is_active')->label('啟用')->default(true),
            ])->columns(2),
            Forms\Components\Section::make('幹部資訊')->schema([
                Forms\Components\Select::make('president_id')->label('社長')->relationship('president', 'name_zh')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('secretary_id')->label('秘書')->relationship('secretary', 'name_zh')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('treasurer_id')->label('財務長')->relationship('treasurer', 'name_zh')->searchable()->preload()->nullable(),
            ])->columns(3),
            Forms\Components\Section::make('聯絡資訊')->schema([
                Forms\Components\TextInput::make('address')->label('地址')->maxLength(255),
                Forms\Components\TextInput::make('city')->label('城市')->maxLength(100),
                Forms\Components\TextInput::make('phone')->label('電話')->maxLength(50),
                Forms\Components\TextInput::make('email')->label('電子郵件')->email()->maxLength(255),
                Forms\Components\TextInput::make('website')->label('網站')->url()->maxLength(255),
                Forms\Components\TextInput::make('line_official_account')->label('LINE 官方帳號')->maxLength(255),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('club_code')->label('代碼')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('社團名稱')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('district.name')->label('地區')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('members_count')->label('會員數')->counts('members')->sortable(),
                Tables\Columns\TextColumn::make('city')->label('城市')->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label('啟用')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('district_id')->label('地區')->relationship('district', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClubs::route('/'),
            'create' => Pages\CreateClub::route('/create'),
            'edit' => Pages\EditClub::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

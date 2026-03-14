<?php
namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DistrictResource extends Resource
{
    protected static ?string $model = District::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = '組織管理';
    protected static ?string $modelLabel = '地區';
    protected static ?string $pluralModelLabel = '地區管理';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('基本資訊')->schema([
                Forms\Components\TextInput::make('name')->label('地區名稱（中文）')->required()->maxLength(255),
                Forms\Components\TextInput::make('name_en')->label('地區名稱（英文）')->maxLength(255),
                Forms\Components\TextInput::make('district_code')->label('地區代碼')->required()->unique(ignoreRecord: true)->maxLength(50),
                Forms\Components\Toggle::make('is_active')->label('啟用')->default(true),
            ])->columns(2),
            Forms\Components\Section::make('聯絡資訊')->schema([
                Forms\Components\TextInput::make('office_address')->label('辦公地址')->maxLength(255),
                Forms\Components\TextInput::make('phone')->label('電話')->maxLength(50),
                Forms\Components\TextInput::make('email')->label('電子郵件')->email()->maxLength(255),
                Forms\Components\TextInput::make('line_official_account')->label('LINE 官方帳號')->maxLength(255),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('district_code')->label('代碼')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('地區名稱')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name_en')->label('英文名稱')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('clubs_count')->label('社團數')->counts('clubs')->sortable(),
                Tables\Columns\TextColumn::make('members_count')->label('會員數')->counts('members')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('啟用')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('建立時間')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

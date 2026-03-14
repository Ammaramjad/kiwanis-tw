<?php
namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = '組織管理';
    protected static ?string $modelLabel = '會員';
    protected static ?string $pluralModelLabel = '會員管理';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('基本資訊')->schema([
                Forms\Components\TextInput::make('member_id')->label('會員編號')->required()->unique(ignoreRecord: true)->maxLength(50),
                Forms\Components\TextInput::make('name_zh')->label('姓名（中文）')->required()->maxLength(255),
                Forms\Components\TextInput::make('name_en')->label('姓名（英文）')->maxLength(255),
                Forms\Components\Select::make('membership_type')->label('會員類型')
                    ->options(['regular' => '一般會員', 'honorary' => '榮譽會員', 'associate' => '附屬會員', 'corporate' => '企業會員'])
                    ->required(),
                Forms\Components\DatePicker::make('join_date')->label('入會日期'),
                Forms\Components\Toggle::make('is_active')->label('啟用')->default(true),
            ])->columns(2),
            Forms\Components\Section::make('組織歸屬')->schema([
                Forms\Components\Select::make('club_id')->label('所屬社團')->relationship('club', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('district_id')->label('所屬地區')->relationship('district', 'name')->searchable()->preload()->nullable(),
            ])->columns(2),
            Forms\Components\Section::make('聯絡資訊')->schema([
                Forms\Components\TextInput::make('phone')->label('電話')->maxLength(50),
                Forms\Components\TextInput::make('email')->label('電子郵件')->email()->maxLength(255),
                Forms\Components\TextInput::make('line_id')->label('LINE ID')->maxLength(100),
                Forms\Components\TextInput::make('address')->label('地址')->maxLength(255),
                Forms\Components\TextInput::make('city')->label('城市')->maxLength(100),
            ])->columns(2),
            Forms\Components\Section::make('職業資訊')->schema([
                Forms\Components\TextInput::make('profession')->label('職業')->maxLength(100),
                Forms\Components\TextInput::make('company')->label('公司/單位')->maxLength(255),
                Forms\Components\Textarea::make('bio')->label('自我介紹')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member_id')->label('會員編號')->sortable()->searchable(),
                Tables\Columns\ImageColumn::make('profile_photo')->label('照片')->circular()->toggleable(),
                Tables\Columns\TextColumn::make('name_zh')->label('姓名')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('club.name')->label('社團')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('district.name')->label('地區')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('phone')->label('電話')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('city')->label('城市')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('profession')->label('職業')->searchable()->toggleable(),
                Tables\Columns\BadgeColumn::make('membership_type')->label('類型')
                    ->colors(['success' => 'regular', 'warning' => 'honorary', 'info' => 'associate', 'primary' => 'corporate'])
                    ->formatStateUsing(fn($state) => match($state) {
                        'regular' => '一般', 'honorary' => '榮譽', 'associate' => '附屬', 'corporate' => '企業', default => $state
                    }),
                Tables\Columns\IconColumn::make('is_active')->label('啟用')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('club_id')->label('社團')->relationship('club', 'name')->searchable()->preload(),
                Tables\Filters\SelectFilter::make('district_id')->label('地區')->relationship('district', 'name')->searchable()->preload(),
                Tables\Filters\SelectFilter::make('membership_type')->label('類型')
                    ->options(['regular' => '一般會員', 'honorary' => '榮譽會員', 'associate' => '附屬會員', 'corporate' => '企業會員']),
                Tables\Filters\TernaryFilter::make('is_active')->label('啟用狀態'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
            'view' => Pages\ViewMember::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

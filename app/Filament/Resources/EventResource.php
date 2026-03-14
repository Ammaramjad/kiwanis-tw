<?php
namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = '活動管理';
    protected static ?string $modelLabel = '活動';
    protected static ?string $pluralModelLabel = '活動管理';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('活動基本資訊')->schema([
                Forms\Components\TextInput::make('title')->label('活動標題（中文）')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('title_en')->label('活動標題（英文）')->maxLength(255)->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->label('活動說明（中文）')->columnSpanFull(),
                Forms\Components\Select::make('club_id')->label('主辦社團')->relationship('club', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('district_id')->label('主辦地區')->relationship('district', 'name')->searchable()->preload()->nullable(),
            ])->columns(2),
            Forms\Components\Section::make('時間與地點')->schema([
                Forms\Components\DateTimePicker::make('start_time')->label('開始時間')->required(),
                Forms\Components\DateTimePicker::make('end_time')->label('結束時間'),
                Forms\Components\TextInput::make('venue')->label('活動場所')->maxLength(255),
                Forms\Components\TextInput::make('address')->label('地址')->maxLength(255),
                Forms\Components\TextInput::make('city')->label('城市')->maxLength(100),
                Forms\Components\TextInput::make('lat')->label('緯度')->numeric(),
                Forms\Components\TextInput::make('lng')->label('經度')->numeric(),
            ])->columns(2),
            Forms\Components\Section::make('聯絡與設定')->schema([
                Forms\Components\TextInput::make('contact_person')->label('聯絡人')->maxLength(100),
                Forms\Components\TextInput::make('contact_phone')->label('聯絡電話')->maxLength(50),
                Forms\Components\TextInput::make('max_participants')->label('最大參加人數')->numeric()->nullable(),
                Forms\Components\Toggle::make('registration_required')->label('需要報名'),
                Forms\Components\Select::make('status')->label('狀態')
                    ->options(['draft' => '草稿', 'published' => '已發布', 'cancelled' => '已取消', 'completed' => '已結束'])
                    ->default('draft')->required(),
                Forms\Components\Select::make('visibility')->label('可見範圍')
                    ->options(['public' => '公開', 'members' => '所有會員', 'district' => '地區會員', 'club' => '社團會員'])
                    ->default('members')->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('活動標題')->sortable()->searchable()->limit(40),
                Tables\Columns\TextColumn::make('club.name')->label('主辦社團')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('start_time')->label('開始時間')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('venue')->label('活動場所')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('city')->label('城市')->searchable()->toggleable(),
                Tables\Columns\BadgeColumn::make('status')->label('狀態')
                    ->colors(['secondary' => 'draft', 'success' => 'published', 'danger' => 'cancelled', 'warning' => 'completed'])
                    ->formatStateUsing(fn($state) => match($state) {
                        'draft' => '草稿', 'published' => '已發布', 'cancelled' => '已取消', 'completed' => '已結束', default => $state
                    }),
                Tables\Columns\TextColumn::make('registrations_count')->label('報名人數')->counts('registrations'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('狀態')
                    ->options(['draft' => '草稿', 'published' => '已發布', 'cancelled' => '已取消', 'completed' => '已結束']),
                Tables\Filters\SelectFilter::make('club_id')->label('社團')->relationship('club', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('start_time', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

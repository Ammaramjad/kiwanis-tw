<?php
namespace App\Filament\Resources;

use App\Filament\Resources\JoinApplicationResource\Pages;
use App\Models\JoinApplication;
use App\Models\Member;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class JoinApplicationResource extends Resource
{
    protected static ?string $model = JoinApplication::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = '系統管理';
    protected static ?string $modelLabel = '入會申請';
    protected static ?string $pluralModelLabel = '入會申請管理';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('申請人資訊')->schema([
                Forms\Components\TextInput::make('name')->label('姓名')->required()->maxLength(255),
                Forms\Components\TextInput::make('phone')->label('電話')->required()->maxLength(50),
                Forms\Components\TextInput::make('email')->label('電子郵件')->email()->required()->maxLength(255),
                Forms\Components\TextInput::make('city')->label('城市')->maxLength(100),
                Forms\Components\TextInput::make('profession')->label('職業')->maxLength(100),
                Forms\Components\Select::make('preferred_club_id')->label('意向社團')->relationship('preferredClub', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Textarea::make('message')->label('申請說明')->rows(3)->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('審核資訊')->schema([
                Forms\Components\Select::make('status')->label('狀態')
                    ->options(['pending' => '待審', 'approved' => '已核准', 'rejected' => '已拒絕'])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')->label('管理員備註')->rows(3)->columnSpanFull(),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('姓名')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('電話')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('電子郵件')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('city')->label('城市')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('preferredClub.name')->label('意向社團')->sortable(),
                Tables\Columns\BadgeColumn::make('status')->label('狀態')
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected'])
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => '待審', 'approved' => '已核准', 'rejected' => '已拒絕', default => $state
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('申請時間')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('狀態')
                    ->options(['pending' => '待審', 'approved' => '已核准', 'rejected' => '已拒絕']),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('核准')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(JoinApplication $record) => $record->update(['status' => 'approved', 'reviewed_at' => now()]))
                    ->visible(fn(JoinApplication $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('拒絕')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(JoinApplication $record) => $record->update(['status' => 'rejected', 'reviewed_at' => now()]))
                    ->visible(fn(JoinApplication $record) => $record->status === 'pending'),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJoinApplications::route('/'),
            'create' => Pages\CreateJoinApplication::route('/create'),
            'edit' => Pages\EditJoinApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }
}

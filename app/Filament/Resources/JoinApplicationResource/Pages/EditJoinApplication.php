<?php
namespace App\Filament\Resources\JoinApplicationResource\Pages;
use App\Filament\Resources\JoinApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditJoinApplication extends EditRecord {
    protected static string $resource = JoinApplicationResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}

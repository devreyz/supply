<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;


class UserResource extends Resource
{
  protected static ?string $model = User::class;

  protected static ?string $navigationIcon = "heroicon-o-users";
  protected static ?string $navigationGroup = "Usuários"; // Definindo o grupo de navegação
  protected static ?string $navigationLabel = "Usuários";
  
  public static function can(string $action, ?Model $record = null): bool
    {
        // Verifica se o usuário autenticado possui a permissão 'admin'
        return auth()->user()->hasRole("admin");
    }

  public static function form(Form $form): Form
  {
    return $form->schema([
      Forms\Components\TextInput::make("name")
        ->required()
        ->maxLength(255),
      Forms\Components\TextInput::make("email")
        ->email()
        ->required()
        ->maxLength(255),
      Forms\Components\DateTimePicker::make("email_verified_at"),
      Forms\Components\Select::make("roles")
        ->multiple() // Permite selecionar múltiplos papéis
        ->relationship("roles", "name") // Relacionamento com o modelo Role
        ->preload() // Precarrega as opções
        ->label("Papéis"),

      // Campo de senha apenas no formulário de criação
      Forms\Components\TextInput::make("password")
        ->password()
        ->required(fn(string $context) => $context === "create") // Apenas no contexto de criação
        ->maxLength(255)
        ->visible(fn(string $context) => $context === "create"), // Esconde no contexto de edição
    ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make("name")->searchable(),
        Tables\Columns\TextColumn::make("email")->searchable(),
        Tables\Columns\TextColumn::make("email_verified_at")
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make("created_at")
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make("updated_at")
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make("roles.name")->label("Papéis"),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\Action::make("resetPassword")
          ->label("Redefinir Senha")
          ->form([
            Forms\Components\TextInput::make("password")
              ->label("Nova Senha")
              ->password()
              ->required()
              ->rules(["min:8", "confirmed"]), // Opcional: Adiciona validação
            Forms\Components\TextInput::make("password_confirmation")
              ->label("Confirmar Nova Senha")
              ->password()
              ->required(),
          ])
          ->action(function (User $record, array $data) {
            $record->update(["password" => bcrypt($data["password"])]);

            // Opcional: Notifique o usuário sobre a alteração da senha
            $record->notify(
              new \App\Notifications\ResetPasswordNotification(
                $data["password"]
              )
            );
          })
          ->requiresConfirmation()
          ->color("warning"), // Personaliza a cor do botão
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
      "index" => Pages\ListUsers::route("/"),
      "create" => Pages\CreateUser::route("/create"),
      "edit" => Pages\EditUser::route("/{record}/edit"),
    ];
  }
}

<?php

namespace App\Admin\Resources;

use App\Admin\Resources\EmailTemplates\Pages;
use App\Admin\Resources\EmailTemplates\RelationManagers;
use App\Models\Emailtpl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailTemplates extends Resource
{
    protected static ?string $model = Emailtpl::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationLabel = '邮件模板';
    
    protected static ?string $modelLabel = '邮件模板';
    
    protected static ?string $pluralModelLabel = '邮件模板';
    
    protected static ?string $navigationGroup = '邮件设置';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('static_template_notice')
                    ->label('📧 邮件模板说明')
                    ->content(new \Illuminate\Support\HtmlString('
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px;">
                            <p style="margin: 0 0 8px 0; color: #475569;">
                                <strong>模板文件位置：</strong><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 3px; color: #0f172a;">resources/email-templates/</code>
                            </p>
                            <p style="margin: 0; color: #64748b; font-size: 14px;">
                                您可以直接修改该目录下的HTML模板文件，此处仅可编辑邮件标题。
                            </p>
                        </div>
                    '))
                    ->columnSpanFull(),
                
                Forms\Components\TextInput::make('tpl_name')
                    ->label('邮件标题')
                    ->required()
                    ->maxLength(255)
                    ->helperText('支持变量：{{site.name}}、{{order.id}}、{{order.amount | money}}、{{customer.email}} 等。更多变量请查看开发文档。'),
                
                Forms\Components\TextInput::make('tpl_token')
                    ->label('模板标识')
                    ->disabled()
                    ->maxLength(255)
                    ->helperText('模板标识符（只读）'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tpl_name')
                    ->label('模板名称')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('tpl_token')
                    ->label('模板标识')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('编辑标题'),
            ])
            ->bulkActions([
                // 邮件模板不允许删除
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
            'index' => Pages\ListEmailtpls::route('/'),
            'edit' => Pages\EditEmailtpl::route('/{record}/edit'),
        ];
    }
}

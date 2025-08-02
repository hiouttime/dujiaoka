<?php

namespace App\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Themes\ThemeManager;
use Illuminate\Support\Facades\Cache;

class ManageThemeSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationLabel = '主题设置';
    protected static ?string $navigationGroup = '外观设置';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'admin.pages.manage-theme-settings';

    public ?array $data = [];
    public string $activeTheme = '';

    public function mount(): void
    {
        $this->data = $this->getCurrentThemeConfig();
        $this->form->fill($this->data);
    }

    protected function getThemeManager(): ThemeManager
    {
        return app(ThemeManager::class);
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->getThemeConfigFields())->statePath('data');
    }

    protected function getThemeConfigFields(): array
    {
        $themeManager = $this->getThemeManager();
        $this->activeTheme = $themeManager->getActiveTheme() ?: 'neon';
        $configFields = $themeManager->getThemeConfigFields($this->activeTheme);
        $fields = [];

        foreach ($configFields as $sectionKey => $section) {
            $sectionFields = [];
            foreach ($section['fields'] as $fieldKey => $field) {
                $sectionFields[] = $this->createFormField($fieldKey, $field);
            }

            $fields[] = Forms\Components\Section::make($section['title'])
                ->description($section['description'] ?? null)
                ->schema($sectionFields)
                ->collapsible();
        }

        return $fields;
    }

    protected function createFormField(string $key, array $config): Forms\Components\Field
    {
        $field = match ($config['type']) {
            'text' => Forms\Components\TextInput::make($key),
            'textarea' => Forms\Components\Textarea::make($key)->rows($config['rows'] ?? 3),
            'color' => Forms\Components\ColorPicker::make($key),
            'select' => Forms\Components\Select::make($key)->options($config['options'] ?? []),
            'switch' => Forms\Components\Toggle::make($key),
            'range' => Forms\Components\TextInput::make($key)->numeric()->minValue($config['min'] ?? 0)->maxValue($config['max'] ?? 100),
            'image' => Forms\Components\FileUpload::make($key)->image()->directory('theme-assets'),
            'code' => Forms\Components\Textarea::make($key)->rows($config['rows'] ?? 10)->extraAttributes(['class' => 'font-mono']),
            default => Forms\Components\TextInput::make($key),
        };

        return $field
            ->label($config['label'] ?? ucfirst($key))
            ->helperText($config['description'] ?? null)
            ->default($config['default'] ?? null);
    }

    protected function getCurrentThemeConfig(): array
    {
        $themeManager = $this->getThemeManager();
        $this->activeTheme = $themeManager->getActiveTheme() ?: 'neon';
        $configFields = $themeManager->getThemeConfigFields($this->activeTheme);
        
        $config = [];
        foreach ($configFields as $section) {
            foreach ($section['fields'] as $key => $field) {
                $config[$key] = $themeManager->getThemeConfigValue($key, $field['default'] ?? null);
            }
        }
        
        return $config;
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $themeManager = $this->getThemeManager();
            $themeManager->setThemeConfig($this->activeTheme, $data);
            Cache::forget("theme-{$this->activeTheme}-config");
            
            Notification::make()->title('主题设置已保存')->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('保存失败')->body($e->getMessage())->danger()->send();
        }
    }

    public function resetToDefaults(): void
    {
        try {
            $themeManager = $this->getThemeManager();
            $themeConfig = $themeManager->getThemeConfig($this->activeTheme);
            $defaults = $themeConfig ? $themeConfig->getDefaultConfig() : [];
            
            $themeManager->setThemeConfig($this->activeTheme, $defaults);
            $this->data = $defaults;
            $this->form->fill($this->data);
            
            Notification::make()->title('已重置为默认设置')->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('重置失败')->body($e->getMessage())->danger()->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')->label('保存设置')->action('save')->color('primary'),
            Action::make('reset')->label('重置为默认')->action('resetToDefaults')->color('gray')->requiresConfirmation(),
        ];
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('保存设置')->submit('save')->color('primary'),
        ];
    }

    public function getTitle(): string
    {
        $themeManager = $this->getThemeManager();
        $themeInfo = $themeManager->getThemeInfo($this->activeTheme ?: 'neon');
        $themeName = $themeInfo['display_name'] ?? ucfirst($this->activeTheme ?: 'neon');
        return "主题设置 - {$themeName}";
    }
}
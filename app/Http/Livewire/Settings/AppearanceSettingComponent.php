<?php

namespace App\Http\Livewire\Settings;

use Illuminate\Support\Facades\URL;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AppearanceSettingComponent extends Component
{
    use LivewireAlert;


    public array $main_ui = ['ui-default', 'ui-clean', 'ui-shady', 'ui-softy'];
    public array $color_mode = ['dark-mode', 'light-mode'];
    public array $sidebar_header_styles = ['is-light', 'is-dark', 'is-theme'];
    public array $skin_colors = ['red', 'blue', 'green', 'egyptian', 'default', 'purple'];
    public $previous, $value;

    public function mount()
    {
        $this->previous = URL::current();
    }


    public function render()
    {
        return view('livewire.settings.appearance-setting-component');
    }

    public function setSkinColor($value)
    {

        setting(['skin_color' => $value]);
        setting()->save();
        return redirect()->to($this->previous);


    }

    public function setHeaderStyle($v)
    {
        setting(['header_color_style' => $v]);
        setting()->save();
        return redirect()->to($this->previous);

    }

    public function setSidebarStyle($value)
    {
        setting(['sidebar_color_style' => $value]);
        setting()->save();
        return redirect()->to($this->previous);

    }

    public function setColorMode($value)
    {
        setting(['color_mode_style' => $value]);
        setting()->save();
        return redirect()->to($this->previous);

    }

    public function setMainUi($value)
    {
        setting(['main_ui_style' => $value]);
        setting()->save();
        return redirect()->to($this->previous);
    }
}

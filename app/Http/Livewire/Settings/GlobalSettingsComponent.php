<?php

namespace App\Http\Livewire\Settings;

use Illuminate\Support\Facades\URL;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class GlobalSettingsComponent extends Component
{
    use LivewireAlert;

    public $app_name, $currency_name, $currency_symbol;

    public $backup_email;

    public $previous, $value;

    public function mount()
    {
        //get the settings from the database
        $this->app_name = setting('app_name');
        $this->currency_name = setting('currency_name');
        $this->currency_symbol = setting('currency_symbol');

        $this->previous = URL::current();
    }


    public function render()
    {
        return view('livewire.settings.global-settings-component');
    }

    public function submit()
    {
        //save the settings to the database
        setting(['currency_name' => $this->currency_name]);
        setting(['currency_symbol' => $this->currency_symbol]);
        setting(['app_name' => $this->app_name]);
        //backup email
        setting()->save();

        //update the app name
        config(['app.name' => $this->app_name]);

        //flash a message
        session()->flash('message', __('Settings saved successfully.'));
        $this->alert('success', __('Settings saved successfully.'));

        return redirect()->to($this->previous);

    }
}

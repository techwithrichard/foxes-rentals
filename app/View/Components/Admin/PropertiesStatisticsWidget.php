<?php

namespace App\View\Components\Admin;

use App\Models\House;
use App\Models\Property;
use Illuminate\View\Component;

class PropertiesStatisticsWidget extends Component
{
    public int $total_properties_count;
    public int $total_single_units_count;
    public int $total_multi_units_count;

    public function __construct()
    {
        $counts = Property::selectRaw('count(*) as count, is_multi_unit')
            ->groupBy('is_multi_unit')
            ->get()
            ->keyBy('is_multi_unit');

        $this->total_single_units_count = $counts[false]['count'] ?? 0;
        $this->total_multi_units_count = $counts[true]['count'] ?? 0;
        $this->total_properties_count = $this->total_single_units_count + $this->total_multi_units_count;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.properties-statistics-widget');
    }
}

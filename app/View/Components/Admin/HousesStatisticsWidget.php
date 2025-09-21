<?php

namespace App\View\Components\Admin;

use App\Enums\HouseStatusEnum;
use App\Models\House;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class HousesStatisticsWidget extends Component
{
    public int $total_houses_count;
    public int $total_occupied_houses_count;
    public int $total_vacant_houses_count;
    public int $total_under_maintenance_houses_count;

    public function __construct()
    {
//        $this->total_occupied_houses_count = House::where('status', HouseStatusEnum::OCCUPIED)->count();
//        $this->total_vacant_houses_count = House::where('status', HouseStatusEnum::VACANT)->count();
//        $this->total_under_maintenance_houses_count = House::where('status', HouseStatusEnum::UNDER_MAINTENANCE)->count();


        $counts = House::selectRaw('count(*) as count, status')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $this->total_occupied_houses_count = $counts->get(HouseStatusEnum::OCCUPIED->value)['count'] ?? 0;
        $this->total_vacant_houses_count = $counts->get(HouseStatusEnum::VACANT->value)['count'] ?? 0;
        $this->total_under_maintenance_houses_count = $counts->get(HouseStatusEnum::UNDER_MAINTENANCE->value)['count'] ?? 0;






        $this->total_houses_count =
            $this->total_occupied_houses_count + $this->total_vacant_houses_count + $this->total_under_maintenance_houses_count;



    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.admin.houses-statistics-widget');
    }
}

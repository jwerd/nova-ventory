<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class MarkProductSold extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $item = $models->first();

        try {
            $price_sold = $fields->get('price_sold', null);
            if(empty($item)) {
                throw new \Exception('Something happened');
            }

            if(!$price_sold) {
                throw new \Exception('No Price Set');
            }

            // Update price with our values
            $item->markItemSold($fields->get('price_sold'));

            return Action::visit('/resources/orders');
        } catch(\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Number::make('Price', 'price_sold')
                ->rules('required')
                ->placeholder('What price did you sell?'),
        ];
    }
}

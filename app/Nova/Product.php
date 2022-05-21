<?php

namespace App\Nova;

use App\Nova\Actions\MarkProductSold;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jwerd\PriceCalc\PriceCalc;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;

class Product extends Resource
{
    use HasTabs;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Item::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'description',
    ];

    protected $effective_tax_rate = .0810;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', )->rules('required', 'max:255'),
            Images::make('Main image', 'main') // second parameter is the media collection name
                ->conversionOnIndexView('thumb') // conversion used to display the image
                ->hideFromIndex(),
            Number::make('Purchase Price', 'price')->rules('required'),
            Number::make('List Price', 'list_price')->rules('required'),
            Text::make('Description')
                ->placeholder('Item purchased from Habitat for Humanity')
                ->rules('max:255'),
            Date::make('Added on', 'created_at')
        ];
    }

    public function fieldsForUpdate(NovaRequest $request)
    {
        return [
            new Tabs('Product', [
                'Info'    => [
                    ID::make()->sortable(),
                    Text::make('Name', )->rules('required', 'max:255'),
                    Images::make('Main image', 'main') // second parameter is the media collection name
                        ->conversionOnIndexView('thumb') // conversion used to display the image
                        ->hideFromIndex(),
//
//                    new Panel('Pricing Information', [
//                        Number::make('Purchase Price', 'price')->rules('required'),
//                        Number::make('List Price', 'list_price')
//                            ->showOnIndex()
//                            ->showOnDetail()
//                            ->hideWhenCreating()
//                            ->hideWhenUpdating()
//                            ->rules('required'),
//                    ]),

                    $this->KeyValueCreate(),
                    $this->KeyValueUpdate(),

                    Number::make('Purchase Price', 'price')->rules('required'),
                    Number::make('List Price', 'list_price')->rules('required'),
                    Text::make('Description')
                        ->placeholder('Item purchased from Habitat for Humanity')
                        ->rules('max:255'),
                    Date::make('Added on', 'created_at')
                        ->hideWhenCreating(),
                ],
                'Calculator' => [
                    Number::make('Purchase Price', 'price')->rules('required'),
                    PriceCalc::make('Pricing Calculator', 'list_price')
                        ->hideWhenCreating()
                        ->hideFromDetail()
                        ->hideFromIndex()
                        ->withMeta(['price' => $this->price])
                        ->rules('required')
                ],
            ]),
        ];
    }

    public function fieldsForCreate(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Name', )->rules('required', 'max:255'),
            Images::make('Main image', 'main') // second parameter is the media collection name
                ->conversionOnIndexView('thumb') // conversion used to display the image
                ->hideFromIndex(),
            Number::make('Purchase Price', 'price')->rules('required'),
            Text::make('Description')
                ->placeholder('Item purchased from Habitat for Humanity')
                ->rules('max:255'),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            MarkProductSold::make()->withMeta(['price' => $this->list_price])
        ];
    }

    protected function KeyValueCreate()
    {
        return KeyValue::make('Dimensions', 'dimension')->onlyOnForms()
            ->keyLabel('Dimension')
            ->disableAddingRows()
            ->disableDeletingRows()
            ->disableEditingKeys()
            ->showOnCreating()
            ->hideWhenUpdating()
            ->resolveUsing(function ($object) {
                return [
                    'H' => 0,
                    'W' => 0,
                    'L' => 0,
                ];
            });
    }

    protected function KeyValueUpdate()
    {
        return KeyValue::make('Dimensions', 'dimension')->onlyOnForms()
            ->keyLabel('Dimension')
            ->disableAddingRows()
            ->disableDeletingRows()
            ->disableEditingKeys()
            ->showOnUpdating()
            ->showOnDetail()
            ->hideWhenCreating()
            ->resolveUsing(function ($object) {
                $h = data_get($object, 'H');
                $w = data_get($object, 'W');
                $l = data_get($object, 'L');
                if(data_get($object, "height")) {
                    $h = data_get($object, 'height');
                    $w = data_get($object, 'depth');
                    $l = data_get($object, 'length');
                }

                return [
                    'H' => $h,
                    'W' => $w,
                    'L' => $l,
                ];
            });
    }
}

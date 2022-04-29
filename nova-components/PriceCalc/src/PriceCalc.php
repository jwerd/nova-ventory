<?php

namespace Jwerd\PriceCalc;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class PriceCalc extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'price-calc';

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request,
                                                            $requestAttribute,
                                                            $model,
                                                            $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = $request[$requestAttribute];
        }
    }

    public function setPrice(NovaRequest $request)
    {
        return $this->withMeta(['price' => $request->all()]);
    }
}
